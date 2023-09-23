<?php
/**
 * Verify user status from Database and inject account information into middleware data
 * Used For Account controller
 * 
 * @deprecated Now we getting user status and role info from TokenStorage
 */
namespace App\Middleware\Accounts\Account;

use System\HTTP\Request;
use System\HTTP\Response;
use App\Models\Accounts\Account\Account as AccountModel;

/**
 * class VerifyAccountDBStatus
 * 
 * @package App\Middleware
 */
class VerifyAccountDbStatus {

    /**
     * Verify user account status in Dataabse and inject account data into middleware
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return array|bool
	 * @throws \Exception 
     */
    public function getVerify(Request $request, Response $response, array $middlewareData) {
        
        # Fields: userId, roleId, notEditableFields
        $userModel = new AccountModel();

        $userRecord = $userModel->fetchFieldsByWhere(
            [
                'userId', 'roleId', 'status', 'notEditableFields'
            ],[
                'userId' => $middlewareData['account']['userId'],
                'roleId' => $middlewareData['account']['roleId']
            ]
        );
        
        // In case if account not found
        if(!$userRecord) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Account not found'
            ], 404);

            return false;
        }

        // Check if status is valid to delete account
        // 0 - inactive (not verified) account
        // 1 - active
        // This statuses above  is able to delete account
        if($userRecord['status'] > 1) {
            
            $response->sendJson([
			    'status' => 'error',
			    'message' => 'Due to the status of your account, you are not able to access this resource'
		    ], 403);

		    return false;
        }

        $middlewareData['userRecord'] = $userRecord;

        return $middlewareData;
    }

}