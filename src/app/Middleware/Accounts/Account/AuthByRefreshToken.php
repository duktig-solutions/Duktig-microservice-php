<?php
/**
 * Middleware class to get new Refreshed token
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Accounts\Account;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

/**
 * Class AuthByRefreshToken
 *
 * @package App\Middleware
 */
class AuthByRefreshToken {
	
	/**
	 * Authenticate refresh token
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param array $middlewareData
	 * @return array|bool
	 * @throws \Exception
	 */
	public function check(Request $request, Response $response, array $middlewareData): bool|array
    {
		
		# Get jwt from Headers with Token key name from config
		$jwt = $request->headers(Config::get()['JWT']['refresh_token']['RefreshTokenKey']);

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
		$decodedJWT = \Lib\Auth\Jwt::decode($jwt, Config::get()['JWT']['refresh_token']);

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

		# Verify payload account details
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
		
		# Check the userId and deviceId
        # Not checking by user email, phone.. because that data depends on provider
        # userId example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw
        # deviceId example: R4tF38GryB4V-QefTMa
        if(!\Lib\Valid::stringLength($decodedJWT['payload']['account']['userId'], 20, 40)  or
             !\Lib\Valid::stringLength($decodedJWT['payload']['account']['deviceId'], 10, 100)) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid userId or deviceId'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;

        }

		# Verify jti - Unique Id of token
		# Encrypted as: \Lib\Auth\Password::encrypt($account['userId'] . $config['payload_secure_encryption_key'] . '__' . $deviceId)
		$builtJti = $decodedJWT['payload']['account']['userId'] . Config::get()['JWT']['refresh_token']['payload_secure_encryption_key'] . '__' . $decodedJWT['payload']['account']['deviceId'];
		  
		if(!\Lib\Auth\Password::verify($builtJti, $decodedJWT['payload']['jti'])) {
                    
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid jti'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;
        }


		# Verify the secureKey in account
		# Encrypted as: \Lib\Auth\Password::encrypt($account['userId'].$config['payload_secure_encryption_key'].$deviceId)
		$builtSecureKey = $decodedJWT['payload']['account']['userId'].Config::get()['JWT']['refresh_token']['payload_secure_encryption_key'].$decodedJWT['payload']['account']['deviceId'];
		
		# The verification algorithm in this case works as follows:
		# The payload contains password encrypted ( string as: user id + config [ account_key ] + deviceId)
		# In the payload of account we also have userId and deviceId
		# The token comes with payload containing the encrypted key and we compare with local, just created key.
		# if encrypted_key_in_payload != key_verified_just_now then the key is invalid
		# However, listening: Radio Italia Anni 60 - on Zenbook Elementary OS ;)
		if(!\Lib\Auth\Password::verify($builtSecureKey, $decodedJWT['payload']['account']['secureKey'])) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Invalid token'
			], 401);

			# Exit the application
			$response->sendFinal();

			return false;

		}

		# Now try to get a token data from TokenStorage
        # Notice! This token data expiration is the same as refreshToken expiration.
        # In case of Admin removes this token, a user cannot access to verify or refresh the token eve if it wasn't expired.
        $storedToken = \Lib\Auth\Storage::get(
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

		$middlewareData['account'] = $decodedJWT['payload']['account'];
		$middlewareData['jwtPayload'] = $decodedJWT['payload'];

		return $middlewareData;

	}

}