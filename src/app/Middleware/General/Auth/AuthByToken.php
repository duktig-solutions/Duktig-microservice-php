<?php
/**
 * Middleware class to validate Auth-Token
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\General\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

/**
 * Class AuthByToken
 *
 * @package App\Middleware
 */
class AuthByToken {
	
    /**
     * Authenticate
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     * @throws \Exception
     */
    public function check(Request $request, Response $response, array $middlewareData) {

        # Get jwt from Headers
        # This should be a base64 encoded string.
        # Example: eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1N...
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
        # This should decode and return an array with payload containing user account information
        $decodedJWT = \Lib\Auth\Jwt::decode($jwt, Config::get()['JWT']['access_token']);
        
        # If decoding status is not ok
        if($decodedJWT['status'] != 'ok') {

            $response->sendJson([
                'status' => 'error',
                'message' => $decodedJWT['message']
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;

        }
        
        # Verify payload account details
        # userId example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw
        # deviceId example: R4tF38GryB4V-QefTMa
		if(empty($decodedJWT['payload']['account']['userId']) or 
            empty($decodedJWT['payload']['account']['deviceId'])) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid Payload'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;

        }

        # Verify the token jti - Unique Id
        # Encrypted as: \Lib\Auth\Password::encrypt($account['userId'].'__'.$config['payload_secure_encryption_key'] . $deviceId),
        $builtJti = $decodedJWT['payload']['account']['userId'].'__'.Config::get()['JWT']['access_token']['payload_secure_encryption_key'] . $decodedJWT['payload']['account']['deviceId'];
        
        if(!\Lib\Auth\Password::verify($builtJti, $decodedJWT['payload']['jti'])) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid jti'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;
        }
        
        # Just started to play "Pink Floyd - Hey You" in Radio Italia Anni 60

        # Now try to get a token data from TokenStorage
        # Notice! This token data expiration is the same as refreshToken expiration.
        # In case of Admin removes this token, a user cannot access to verify or refresh the token eve if it wasn't expired.
        # This will return array like this:
        /*
        Array
        (
            [dateLogin] => 2021-03-14 15:21:17
            [loginIp] => 192.168.2.152
            [deviceId] => ZenTestSessionsRoleTest
            [userAgent] => PostmanRuntime/7.26.8
            [status] => 1
            [roleId] => a45rzo01f3
            [displayName] => JerrSer99
            [firstName] => Jerryk
            [lastName] => Serontoko
            [lastActive] => 2021-03-14 15:21:17
        )
        */
        $storedToken = \Lib\Auth\TokenStorage::get(
            $decodedJWT['payload']['account']['userId'], 
            $decodedJWT['payload']['account']['userId'].'_'.sha1($decodedJWT['payload']['account']['deviceId'])
        );

        # The storage not contains a token with this userId and device
        if(!$storedToken) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Token expired'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;

        }

        # We should check the user Status and Role from Token Storage
        # Last action from Admin can be performed in TokenStorage 
        # but the token by itself don't know about it.
        # So, final info about User status and roleId comes from TokenStorage
        
        # Add Data From Session and Profile        
        $middlewareData['account'] = array_merge($decodedJWT['payload']['account'], $storedToken);
        
        # Define User Permissions to allow access to this resource
        if(!$this->checkPermissions($middlewareData['account']['roleId'])) {

            # Access forbidden to this resource
		    $response->sendJson([
			    'status' => 'error',
			    'message' => 'Forbidden'
		    ], 403);

		    # Exit the application
		    $response->sendFinal();

		    return false;

	    }

        return $middlewareData;

    }

	/**
	 * Check user Permissions, if user Role allowed to access this resource
	 *
	 * @access protected
	 * @param string $userRoleId
	 * @return bool
	 * @todo Finalize this with new functionality
	 */
    protected function checkPermissions(string $userRoleId) : bool {
		return true;
		/*
	    # Get matched route
	    $route = \System\HTTP\Router::getRoute();

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
		*/
    }

}