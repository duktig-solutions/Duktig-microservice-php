<?php
/**
 * User Controller
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
*/
namespace App\Controllers\Auth;

use Lib\Validator;
use System\Request;
use System\Response;
use App\Models\Auth\User as UserModel;

/**
 * Class User
 *
 * @package App\Controllers
 */
class User {

    /**
     * Register user account
     *
     * @access public
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @throws \Exception
     * @return bool
     */
    public function registerAccount(Request $request, Response $response, array $middlewareData) : bool {

        // Validate User Data
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'firstName' => 'string_length:2:15',
                'lastName' => 'string_length:2:20',
                'email' => 'email',
                'password' => 'password:6:256',
                'phone' => 'string_length:6:20',
	            'roleId' => 'one_of:'.USER_ROLE_SERVICE_PROVIDER.':'.USER_ROLE_CLIENT
            ],
            [
                'general' => 'exact_keys_values'
            ]
        );

        $userModel = new UserModel();

        // Check if email address is valid then try to check if exists in db.
        if(!isset($validation['email'])) {

            // Email entered valid. Then try to check if exists in db.
            if($userModel->emailExistsById($request->input('email'))) {
                $validation['email'] = ['Email address is already registered'];
            }

        }

        // There are errors in validation
        if(!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        // Insert User and get ID
        $id = $userModel->insertRow([
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'phone' => $request->input('phone'),
	        'status' => USER_STATUS_NOT_VERIFIED,
	        'roleId' => $request->input('roleId'),
        ]);

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Signed up successfully',
            //'id' => $id
        ], 200);

        return true;
    }

    public function getAccount(Request $request, Response $response, array $middlewareData) : bool {

        $userModel = new UserModel();

        $userRecord = $userModel->fetchPublicRow([
            'userId' => $middlewareData['account']['userId'],
            'email' => $middlewareData['account']['email']
        ]);

        // In case if account not found
        if(!$userRecord) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Account not found'
            ], 404);

            return false;
        }

        $response->sendJson($userRecord, 200);

        return true;
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
    public function updateAccount(Request $request, Response $response, array $middlewareData) : bool {

        $userModel = new UserModel();

        $userRecord = $userModel->fetchRow([
            'userId' => $middlewareData['account']['userId'],
            'email' => $middlewareData['account']['email']
        ]);

        // In case if account not found
        if(!$userRecord) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Account not found'
            ], 404);

            return false;
        }

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
            'userId' => $userRecord['userId'],
            'email' => $userRecord['email']
        ]);

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Updated successfully'
        ], 200);

        return true;
    }

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
    public function patchAccount(Request $request, Response $response, array $middlewareData) : bool {

        $userModel = new UserModel();

        $userRecord = $userModel->fetchRow([
            'userId' => $middlewareData['account']['userId'],
            'email' => $middlewareData['account']['email']
        ]);

        // In case if account not found
        if(!$userRecord) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Account not found'
            ], 404);

            return false;
        }

        // Validate User Data
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'firstName' => 'string_length:2:10:!required',
                'lastName' => 'string_length:2:10:!required',
                'password' => 'password:6:256:!required',
                'phone' => 'string_length:6:20:!required'
            ],
            [
                'general' => 'at_least_one_value|no_extra_values'
            ]
        );

        // There are errors in validation
        if(!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        $allowedFields = [
            'firstName',
            'lastName',
            'password',
            'phone'
        ];

        $updateFields = [];

        foreach ($allowedFields as $field) {
            if($request->input($field)) {
                $updateFields[$field] = $request->input($field);
            }
        }

        // Update User account
        $userModel->updateRow($updateFields, [
            'userId' => $userRecord['userId'],
            'email' => $userRecord['email']
        ]);

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Patched successfully',
        ], 200);

        return true;
    }

}
