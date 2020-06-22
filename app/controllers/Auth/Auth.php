<?php
/**
 * Authorization Controller
 * Authorize and get token
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Auth;

use System\Logger;
use Lib\Validator;
use System\Request;
use System\Response;
use App\Models\Auth\User as UserModel;

/**
 * Class Auth
 *
 * @package App\Controllers
 */
class Auth {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws \Exception
     */
    public function Authorize(Request $request, Response $response, array $middlewareData) : bool {

        // Validate User Data
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'email' => 'required|email',
                'password' => 'required|password:6:256',

            ],
            # Extra validation for all fields
            [
                'account' => 'exact_keys_values'
            ]
        );

        # There are errors in validation
        if(!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        # Try to get account
        $userModel = new UserModel();

        $account = $userModel->fetchRow([
            'email' => $request->input('email')
        ]);

        # Account with requested email not exists
        if(!$account) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Incorrect credentials'
            ], 401);

            return false;
        }

        # Verify the password hash
        if(!\Lib\Auth\Password::verify($request->input('password'), $account['password'])) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Incorrect credentials'
            ], 401);

            return false;
        }

	    # Check the account status
        if($account['status'] != USER_STATUS_ACTIVE) {

	        $response->sendJson([
		        'status' => 'error',
		        'message' => 'Due to the status of your account, you are not able to access this resource'
	        ], 403);

	        return false;
        }

        # Let's update User login date
        $userModel->updateRow([
            'dateLastLogin' => date('Y-m-d H:i:s')
        ],[
            'userId' => $account['userId'],
            'email' => $account['email']
        ]);

        # Generate the token
	    $access = \App\Lib\Auth\Jwt::generate($account);

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Signed in successfully',
            'lastLogin' => $account['dateLastLogin'],
            'access_token' => $access['access_token'],
	        'expires_in' => $access['expires_in'],
	        'refresh_token' => $access['refresh_token']
        ]);

        return true;

    }

    public function RefreshToken(Request $request, Response $response, array $middlewareData) : bool {

	    # Before Continue, let's fetch this user again from Database
	    # We have to check the status and other information
	    $userModel = new UserModel();

	    $account = $userModel->fetchRow([
		    'userId' => $middlewareData['account']['userId']
	    ]);

	    # Account with requested Id doesn't exists
	    if(!$account) {

		    $response->sendJson([
			    'status' => 'error',
			    'message' => 'Account not exists'
		    ], 401);

		    return false;
	    }

	    # Check the account status
	    if($account['status'] != USER_STATUS_ACTIVE) {

		    $response->sendJson([
			    'status' => 'error',
			    'message' => 'Due to the status of your account, you are not able to access this resource'
		    ], 403);

		    return false;
	    }

	    $response->sendJson([
		    'status' => 'OK',
		    'message' => 'Access token regenerated successfully',
		    'access_token' => \App\Lib\Auth\Jwt::AccessToken($account),
		    'expires_in' => \App\Lib\Auth\Jwt::AccessTokenExpiresIn()
	    ]);

	    return true;
    }

}