<?php
/**
 * User Signup Controller by Email/Password
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
 * Class Signup
 *
 * @package App\Controllers
 */
class Signup {

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
    public function process(Request $request, Response $response, array $middlewareData) : bool {

        // Validate User Data
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'firstName' => 'string_length:2:15',
                'lastName' => 'string_length:2:20',
                'email' => 'email',
                'password' => 'password:6:256',
                'displayName' => 'string_length::100:!required',
            ],
            [
                'general' => 'exact_keys_values'
            ]
        );

        $userModel = new AccountModel();

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
        // @todo catch exception!
        $id = $userModel->insertRow([
            'userId' => \Lib\DataGenerator::createString(30,30,true,true,false),
            'roleId' => ACCOUNTS_DEFAULT_ROLE,
            'status' => USER_STATUS_NOT_VERIFIED,
            'email' => $request->input('email'),
            'password' => $request->input('password'),            
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'displayName' => $request->input('displayName'),
            'provider' => ACCOUNT_PROVIDER_BASIC_SIGN_UP,
            'profileCompleteLevel' => 1,
	        'notEditableFields' => json_encode(['email']),
            'dateRegistered' => date('Y-m-d H:i:s')
        ]);

        // @todo Send Event: user_registered

        $response->sendJson([
            'status' => 'OK',
            'message' => 'Signed up successfully',
            //'id' => $id
        ], 200);

        return true;
    }    
}
