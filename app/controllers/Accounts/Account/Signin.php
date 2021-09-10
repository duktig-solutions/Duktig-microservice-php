<?php
/**
 * Authorization Controller
 * Sign in with email and password - get token
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Accounts\Account;

use Lib\Validator;
use System\HTTP\Request;
use System\HTTP\Response;
use App\Models\Accounts\Account\Account as AccountModel;
use App\Lib\Accounts\Account\Auth;
use System\Config;

/**
 * Class Signin
 *
 * @package App\Controllers
 */
class Signin {

    /**
     * Authorize User by email/password and response with token
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws \Exception
     */
    public function process(Request $request, Response $response, array $middlewareData) : bool {
        
        # Validate User Data
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'email' => 'required|email',
                'password' => 'required|password:6:256'

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
        
        # Validate X-Device-Id in headers
        $validation = Validator::validateDataStructure(
            $request->headers(),
            [
                'X-Device-Id' => 'string_length:10:100'
            ]
        );

        # There are errors in validation
        if(!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        # Try to get account
        $authModel = new AccountModel();
        
        # Select specific rows for Authorization
	    $account = $authModel->fetchFieldsByWhere(
            ['userId', 'roleId', 'status', 'firstName', 'lastName', 'displayName', 'email', 'password', 'dateLastLogin'],
            ['email' => $request->input('email')]
        );

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
        # This should be 0 or 1.
        if($account['status'] > USER_STATUS_ACTIVE) {

	        $response->sendJson([
		        'status' => 'error',
		        'message' => 'Due to the status of your account, you are not able to access this resource'
	        ], 403);

	        return false;
        }

        # Let's update User login date
        $authModel->updateLastAuthById(
            $account['userId'],
            [
                'dateLastLogin' => date('Y-m-d H:i:s'),
                'lastLoginIP' => \Lib\HTTP\ClientInfo::ipAddress()
            ]
        );

        # Generate the token
        # Each generated token should be related to one device Id
        $auth = Auth::Authorize($account, $request->headers('X-Device-Id'));

        $response->sendJson([
            'lastLogin' => $account['dateLastLogin'],
            'access_token' => $auth['access_token'],
	        'expires_in' => $auth['expires_in'],
	        'refresh_token' => $auth['refresh_token']
        ]);

        return true;

    }

	/**
	 * Refresh authorized token (get new)
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
    public function RefreshToken(Request $request, Response $response, array $middlewareData) : bool {

	    # Before Continue, let's fetch this user again from Database
	    # We have to check the status and other information
	    $authModel = new AccountModel();

	    $account = $authModel->fetchRow([
		    'userId' => $middlewareData['account']['userId'],
            'roleId' => $middlewareData['account']['roleId']
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
	    if($account['status'] > USER_STATUS_ACTIVE) {

		    $response->sendJson([
			    'status' => 'error',
			    'message' => 'Due to the status of your account, you are not able to access this resource'
		    ], 403);

		    return false;
	    }

	    $response->sendJson([
		    'status' => 'OK',
		    'message' => 'Access token regenerated successfully',
		    'access_token' => JwtTokenGenerator::AccessToken($account, $middlewareData['account']['deviceId']),
		    'expires_in' => JwtTokenGenerator::AccessTokenExpiresIn()
	    ]);

	    return true;
    }

}