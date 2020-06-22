<?php
/**
 * User Authorization and Authentication middleware class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\General;

use System\Request;
use System\Response;

/**
 * Class Auth
 *
 * @package App\Middleware
 */
class Auth {

	/**
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return array|bool
	 */
    public function AuthByKey(Request $request, Response $response, array $middlewareData) {

        # Get Auth config
        $config = \System\Config::get()['Auth'];

        # Get Auth key from Headers
        $authKey = $request->headers($config['AuthKey']);

        if($authKey == $config['AuthKeyValue']) {
            return $middlewareData;
        }

        $response->sendJson([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);

        # Exit the application
        $response->sendFinal();

        return false;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     * @throws \Exception
     */
    public function Authenticate(Request $request, Response $response, array $middlewareData) {

        # Get jwt from Headers
        $jwt = $request->headers('Access-Token');

        # Check if jwt is empty
        if(!$jwt) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;
        }

        # Decode JWT
        $decodedJWT = \Lib\Auth\Jwt::decode($jwt, \System\Config::get()['JWT']['access_token']);

        # If the returned value is not an empty array
        if($decodedJWT['status'] != 'ok') {

            $response->sendJson([
                'status' => 'error',
                'message' => $decodedJWT['message']
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;

        }

        # Check the account Email and ID
        # The JWT Payload should have email and userId inside account
        if(!\Lib\Valid::email($decodedJWT['payload']['account']['email']) or
            !\Lib\Valid::id($decodedJWT['payload']['account']['userId'])) {

            $response->sendJson([
                'status' => 'error',
                'message' => $decodedJWT['message']
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;

        }

        # Define User Permissions to allow access to this resource
        $userRoleId = $decodedJWT['payload']['account']['roleId'];

        # Access forbidden to this resource
	    if(!$this->checkPermissions($userRoleId)) {

		    $response->sendJson([
			    'status' => 'error',
			    'message' => 'Forbidden'
		    ], 403);

		    # Exit the application
		    $response->sendFinal();

		    return false;

	    }

        $middlewareData['account'] = $decodedJWT['payload']['account'];
        $middlewareData['jwtPayload'] = $decodedJWT['payload'];

        return $middlewareData;

    }

	public function AuthenticateRefreshToken(Request $request, Response $response, array $middlewareData) {

		# Get jwt from Headers
		$jwt = $request->headers('Refresh-Token');

		# Check if jwt is empty
		if(!$jwt) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Unauthorized'
			], 401);

			# Exit the application
			$response->sendFinal();

			return false;
		}

		# Decode JWT
		$decodedJWT = \Lib\Auth\Jwt::decode($jwt, \System\Config::get()['JWT']['refresh_token']);

		# If the returned value is not an empty array
		if($decodedJWT['status'] != 'ok') {

			$response->sendJson([
				'status' => 'error',
				'message' => $decodedJWT['message']
			], 401);

			# Exit the application
			$response->sendFinal();

			return false;

		}

		# Check the account ID
		# The JWT Payload should have userId inside account
		if(!\Lib\Valid::id($decodedJWT['payload']['account']['userId'])) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Invalid token'
			], 401);

			# Exit the application
			$response->sendFinal();

			return false;

		}

		# Check the account email
		# The JWT Payload should have user email inside account
		if(!\Lib\Valid::email($decodedJWT['payload']['account']['email'])) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Invalid token'
			], 401);

			# Exit the application
			$response->sendFinal();

			return false;

		}

		# Verify the key of user account
		$accountLocalBuildKey = $decodedJWT['payload']['account']['userId'] . \System\Config::get()['JWT']['refresh_token']['account_key'] . $decodedJWT['payload']['account']['email'];
		$accountPayloadKey = $decodedJWT['payload']['account']['account_key'];

		# The verification algorithm in this case works as follows:
		# The payload contains password encrypted ( string as: user id + config [ account_key ] + user email)
		# In the payload of account we also have user id and email
		# The token comes with payload containing the encrypted key and we compare with local, just created kay.
		# if encrypted_key_in_payload != key_verified_just_now then the key is wrong
		# However, listen music: Omni Trio - The Haunted Science
		if(!\Lib\Auth\Password::verify($accountLocalBuildKey, $accountPayloadKey)) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Invalid token'
			], 401);

			# Exit the application
			$response->sendFinal();

			return false;

		}

		$middlewareData['account'] = $decodedJWT['payload']['account'];
		$middlewareData['jwtPayload'] = $decodedJWT['payload'];

		return $middlewareData;

	}

	/**
	 * Check user Permissions, if user Role allowed to access this resource
	 *
	 * @access protected
	 * @param int $userRoleId
	 * @return bool
	 */
    protected function checkPermissions(int $userRoleId) : bool {

	    # Get matched route
	    $route = \System\HttpRouter::getRoute();

	    # If route configuration not contains "rolesAllowed", assuming that the access is granted to all.
	    if(!isset($route['rolesAllowed'])) {
	    	return true;
	    }

	    # If route configuration "rolesAllowed" contains "*", assuming that the access is granted to all.
	    if(in_array(USER_ROLE_ANY, $route['rolesAllowed'])) {
	    	return true;
	    }

	    # Now, Let's check, if user Role contains in configuration
	    if(in_array($userRoleId, $route['rolesAllowed'])) {
		    return true;
	    }

	    # Route not allowed to this user
	    return false;

    }

}