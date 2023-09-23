<?php
/**
 * User Controller
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

/**
 * Class Account
 *
 * @package App\Controllers
 */
class Account {
    
    public function get(Request $request, Response $response, array $middlewareData) : bool {
        
        # Fields: userId, roleId, notEditableFields
        $userModel = new AccountModel();

        $userRecord = $userModel->fetchRow([
            'userId' => $middlewareData['account']['userId'],
            'roleId' => $middlewareData['account']['roleId']
        ]);
        
        $response->sendJson($userRecord, 200);

        return true;
    }

    public function getLoginSessions(Request $request, Response $response, array $middlewareData) : bool {
        
        # Let's get Logins from Auth TokenStorage
        $storedTokens = \Lib\Auth\TokenStorage::getAll($middlewareData['account']['userId']);
        
        $result = [];
        
        static::array_sort_by_column($storedTokens, 'dateLogin');

        foreach($storedTokens as $loginKey => $loginSession) {
            
            $result[] = [
                'sessionId' => $loginKey,
                'dateLogin' => $loginSession['dateLogin'],
                'loginIp' => $loginSession['loginIp'],
                'deviceId' => $loginSession['deviceId'],
                'userAgent' => $loginSession['userAgent'],
                'currentDevice' => ($middlewareData['account']['deviceId'] == $loginSession['deviceId']) ? 'Yes' : 'No'
            ];
        }

        $response->sendJson($result);

        return True;

    }

    public static function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }
    
        array_multisort($sort_col, $dir, $arr);
    }
    

    /**
     * Update account works at "PUT" request and requires total data change.
     *
     * @access public
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @throws \Exception
     * @return bool
     */
    /*
    public function updateAccount(Request $request, Response $response, array $middlewareData) : bool {

        // Validate User Data
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'firstName' => 'required|string_length:2:10',
                'lastName' => 'required|string_length:2:10',
                'password' => 'required|password:6:256',
                'phone' => 'required|string_length:6:20'
            ],
            [
                'general' => 'exact_keys_values'
            ]
        );

        // There are errors in validation
        if(!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        // Update User account
        $userModel->updateRow([
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'password' => $request->input('password'),
            'phone' => $request->input('phone')
        ], [
            'userId' => $middlewareData['account']['userId'],
            'deviceId' => $middlewareData['account']['deviceId']
        ]);

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Updated successfully'
        ], 200);

        return true;
    }
    */

    /**
     * Patch account allowed to change/edit only parts of user account data
     *
     * @access public
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @throws \Exception
     * @return bool
     */
    public function patch(Request $request, Response $response, array $middlewareData) : bool {

        $dobDateFrom = date('Y-m-d', strtotime('-90 Year'));
	    $dobDateTo = date('Y-m-d', strtotime('-16 Year'));

        // Validate User Data
        $validationRules = [
            'firstName' => 'string_length:2:10:!required:!empty',
            'lastName' => 'string_length:2:10:!required',
            'displayName' => 'string_length::100:!required',
            'password' => 'password:6:256:!required',
            'email' => 'email:!required',
            'gender' => 'one_of:0:1:2:!required',
            'dob' => 'date_between:'.$dobDateFrom.':'.$dobDateTo.':Y-m-d:!required',
            //"photo": null,
            'aboutMe' => 'string_length:0:1500:!required',
            'country' => 'string_length:0:100:!required',
            'state' => 'string_length:0:100:!required',
            'city' => 'string_length:0:100:!required',
            'zip_code' => 'string_length:0:10:!required',
            'address_line1' => 'string_length:0:100:!required',
            'address_line2' => 'string_length:0:100:!required',
            'phone' => 'string_length:0:100:!required',
            'website' => 'string_length:0:250:!required'
        ];

        $allowedFieldsToEdit = [
            'firstName',
            'lastName',
            'displayName',
            'password',
            'email',
            'gender',
            'dob',
            'aboutMe',
            'country',
            'state',
            'city',
            'zip_code',
            'address_line1',
            'address_line2',
            'phone',
            'website'
        ];

        # Fields in token storage for profile that allowed to update
        $tokenStorageProfileDataFields = [
            'displayName',
            'firstName',
            'lastName'
        ];

        # Data to update in token storage for profile
        $tokenStorageProfileData = [];
        
        // Figure out not editable fields
        $notEditableFields = json_decode($middlewareData['userRecord']['notEditableFields'], true);

        # Remove fields from validation and allowed to edit in database
        # which are not editable for this account
        foreach ($notEditableFields as $field) {
            unset($validationRules[$field]);
            unset($allowedFieldsToEdit[$field]);
        }
        
        $validation = Validator::validateJson(
            $request->rawInput(),
            $validationRules,
            [
                'general' => 'at_least_one_value|no_extra_values'
            ]
        );

        // There are errors in validation
        if(!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        $updateFields = [];

        foreach ($allowedFieldsToEdit as $field) {
            if($request->input($field)) {
                $updateFields[$field] = $request->input($field);

                # If there are data to update in token storage for profile
                if(in_array($field, $tokenStorageProfileDataFields)) {
                    $tokenStorageProfileData[$field] = $request->input($field);
                }
            }
        }
        
        // Set last action of user
        $updateFields['dateLastAction'] = date('Y-m-d H:i:s');
        
        $userModel = new AccountModel();

        // Update User account
        $userModel->updateRow(
            $updateFields, 
            [
                'userId' => $middlewareData['account']['userId'],
                'roleId' => $middlewareData['account']['roleId']
            ]
        );

        # Update Profile in token storage if some data
        if(!empty($tokenStorageProfileData)) {
            
            \Lib\Auth\TokenStorage::updateProfile(
                $middlewareData['account']['userId'], 
                $tokenStorageProfileData
            );

        }
        
        $response->sendJson([
            'status' => 'OK',
            'message' => 'Patched successfully',
        ], 200);

        return true;
    }

    public function delete(Request $request, Response $response, array $middlewareData) : bool {

        $updateFields = [
            'status' => USER_STATUS_SELF_TERMINATED,
            'dateLastAction' => date('Y-m-d H:i:s')
        ];

        $userModel = new AccountModel();

        # Update User account
        $userModel->updateRow($updateFields, [
            'userId' => $middlewareData['account']['userId'],
            'roleId' => $middlewareData['account']['roleId']
        ]);

        # Delete profile and sessions from token storage
        \Lib\Auth\TokenStorage::deleteAllSessions($middlewareData['account']['userId']);  
        \Lib\Auth\TokenStorage::deleteProfile($middlewareData['account']['userId']);

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Account deleted',
        ], 200);

        return true;
    }
    
    public function deleteLoginSession(Request $request, Response $response, array $middlewareData) : bool {

        # Try to get it
        $sessionData = \Lib\Auth\TokenStorage::get($middlewareData['account']['userId'], $request->paths(1));

        if(!$sessionData) {

            $response->sendJson([
			    'status' => 'error',
			    'message' => 'Session not found'
		    ], 404);

		    return false;

        }

        \Lib\Auth\TokenStorage::delete($middlewareData['account']['userId'], $request->paths(1));

        $response->sendJson([
            'status' => 'ok',
            'message' => 'Session deleted'
        ], 200);

        return True;

    }

    public function deleteAllLoginSessions(Request $request, Response $response, array $middlewareData) : bool {

        # Try to delete all sessions
        $deletedCount = \Lib\Auth\TokenStorage::deleteAllSessions($middlewareData['account']['userId']);

        $response->sendJson([
            'status' => 'ok',
            'message' => 'Sessions deleted',
            'count' => $deletedCount
        ], 200);

        return True;

    }

}
