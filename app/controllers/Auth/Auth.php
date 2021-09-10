<?php
/**
 * Duktig Microservices Authorization service.
 */
namespace App\Controllers\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use Lib\Validator;
use System\Logger;
use System\Config;

class Auth {

    public function perform(Request $request, Response $response, array $middlewareData) : bool {
        
        // # set to headers and finish        
        // $response->header('X-Account-Info', base64_encode('abc1223').'__'.mt_rand(1,10));
        // $response->header('X-Micro-Host-Info', 'http://duktig.microservice');

        // $response->sendJson([
        //     'status' => 'ok',
        //     'message' => 'Authorized'
        // ], 200);

        // return True;

        # Validate the Token 
        $validation = Validator::validateDataStructure(
            $request->headers(),
            [
                'Access-Token' => 'required|string_length:400:1000'
            ]
        );
        
        if(!empty($validation)) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);

            # Exit the application
            $response->sendFinal();

            return false;
        }
        
        $jwt = $request->headers('Access-Token');

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
        # In case of Admin removes this token, a user cannot access to verify or refresh the token even if it wasn't expired.
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
        
        //Logger::log(print_r($jwt, true), Logger::INFO,null, null,'auth.log');      
                
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

        // @todo REQUIRE LIKE THIS !
        // Authorization "bearer SecretForOAuthServer";
        
        Logger::log(print_r($request->headers(), true), Logger::INFO,null, null,'auth.log');
        
        // # set to headers and finish        
        // $response->header('X-Account-Info', base64_encode('abc1223').'__'.mt_rand(1,10));
        // $response->header('X-Micro-Host-Info', 'http://duktig.microservice');

        // $response->sendJson([
        //     'status' => 'ok',
        //     'message' => 'Authorized'
        // ], 200);

        // return True;

        # @todo check permissions
        
        # set to headers and finish        
        $response->header('X-Account-Info', json_encode($storedToken));

        //$response->header('X-Account-Info', base64_encode('abc1223').'__'.mt_rand(1,10));
        $response->header('X-Micro-Host-Info', 'http://duktig.microservice'); // $request->headers('X-Original-Uri')

        // $response->header('X-Account-Info', base64_encode(json_encode([
        //     'userId' => $storedToken['userId'],
        //     'roleId' => $storedToken['roleId'],
        // ])));
        
        $response->sendJson([
            'status' => 'ok',
            'message' => 'Authorized'
        ], 200);
        
        return True;

    }

}
