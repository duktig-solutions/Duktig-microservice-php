<?php
/**
 * RefreshToken
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

/**
 * Class RefreshToken
 *
 * @package App\Controllers
 */
class RefreshToken {

    /**
	 * Refresh authorized token (get new)
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
    public function process(Request $request, Response $response, array $middlewareData) : bool {
		
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

		# Validate the device Id
		# Previously created access token contains first login deviceId
		# So this Refresh token also should contain the same
		if($request->headers('X-Device-Id') != $middlewareData['account']['deviceId']) {
			
			$response->sendJson([
			    'status' => 'error',
			    'message' => 'Incorrect X-Device-Id'
		    ], 401);

		    return false;
		}

	    # Before Continue, let's fetch this user again from Database
	    # We have to check the status and other information
	    $authModel = new AccountModel();

		# Select specific rows for Authorization
	    $account = $authModel->fetchFieldsByWhere(
            [
				'userId', 'firstName', 'lastName', 'displayName', 'email', 'password', 'phone', 'dateRegistered', 'dateUpdated', 'dateLastLogin', 'roleId', 'status'],
            [
				'userId' => $middlewareData['account']['userId']
			]
        );

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

		# Assuming, the Nginx proxy passed with client IP 
        #    (instead of docker container ip)
        if(!empty($request->headers('X-Real-Ip'))) {
            $userIpAddress = $request->headers('X-Real-Ip');
        } else {
            $userIpAddress = \Lib\HTTP\ClientInfo::ipAddress();
        }

		# Let's update User login date
        $authModel->updateLastAuthById(
            $account['userId'],
            [
                'dateLastLogin' => date('Y-m-d H:i:s'),
                'lastLoginIP' => $userIpAddress
            ]
        );

		# Make a token account data fresh ;)
		$tokenAccount = [
			'userId' => $account['userId'],
			'displayName' => $account['displayName'],
			'firstName' => $account['firstName'],
			'lastName' => $account['lastName'],
			'roleId' => $account['roleId'],
			'status' => $account['status']
		];	

		# Generate the token
        # Each generated token should be related to one device Id
        $auth = Auth::Authorize($tokenAccount, $request);

	    $response->sendJson([
		    'status' => 'OK',
		    'message' => 'Access token regenerated successfully',
		    'access_token' => $auth['access_token'],
		    'expires_in' => $auth['expires_in']
	    ]);

	    return true;
    }

}