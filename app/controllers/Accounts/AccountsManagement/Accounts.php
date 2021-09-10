<?php
/**
 * Accounts Controller
 * This controller only available to access for Admin. Checking permissions in Auth middleware.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Accounts\AccountsManagement;

use Lib\Validator;
use System\HTTP\Request;
use System\HTTP\Response;
use App\Models\Accounts\Account\Account as AccountModel;
use App\Models\Accounts\AccountsManagement\Accounts as AccountsModel;

/**
 * Class Accounts
 *
 * @package App\Controllers
 */
class Accounts {

	/**
	 * Get User Accounts
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function getAll(Request $request, Response $response, array $middlewareData) : bool {

		$offset = $request->get('offset', 0);
		$limit = $request->get('limit', 100);

		$validation = Validator::validateDataStructure(
			$request->get(),
			[
				'offset' => 'int_range:0',
				'limit' => 'int_range:1'
			]
		);

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$usersModel = new AccountsModel();

		$response->sendJson(
			$usersModel->fetchRows($offset, $limit)
		);

		return true;

	}

	/**
	 * Get user account by id
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function getByIdStr(Request $request, Response $response, array $middlewareData) : bool {

		$userId = $request->paths(1);

		$validation = Validator::validateDataStructure(
			[
				'userId' => $userId
			],
			[
				'userId' => 'id'
			]
		);

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 404);
			return false;
		}

		$usersModel = new AccountsModel();

		$userRecord = $usersModel->fetchRow([
			'userId' => $userId
		]);

		# In case if account not found
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
	 * Register user account
	 *
	 * @access public
	 * @param Request $request
	 * @param Response $response
	 * @param array $middlewareData
	 * @throws \Exception
	 * @return bool
	 */
	public function create(Request $request, Response $response, array $middlewareData) : bool {

		# Validate User Data
		$validation = Validator::validateJson(
			$request->rawInput(),
			[
				'firstName' => 'string_length:2:15',
				'lastName' => 'string_length:2:20',
				'email' => 'email',
				'password' => 'password:6:256',
				'phone' => 'string_length:6:20',
				'comment' => 'string_length:0:255:!required',
                // @todo figure out this
				//'roleId' => 'one_of:'.USER_ROLE_SERVICE_PROVIDER.':'.USER_ROLE_CLIENT.':'.USER_ROLE_ADMIN.':'.USER_ROLE_SUPER_ADMIN.':'.USER_ROLE_DEVELOPER,
				'status' => 'one_of:'.USER_STATUS_ACTIVE.':'.USER_STATUS_NOT_VERIFIED
			],
			[
				'general' => 'exact_keys_values'
			]
		);

		$userModel = new AccountModel();

		# Check if email address is valid then try to check if exists in db.
		if(!isset($validation['email'])) {

			# Email entered valid. Then try to check if exists in db.
			if($userModel->emailExistsById($request->input('email'))) {
				$validation['email'] = ['Email address is already registered'];
			}

		}

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		# Insert User and get ID
		$id = $userModel->insertRow([
			'firstName' => $request->input('firstName'),
			'lastName' => $request->input('lastName'),
			'email' => $request->input('email'),
			'password' => $request->input('password'),
			'phone' => $request->input('phone'),
			'comment' => $request->input('comment'),
			'roleId' => $request->input('roleId'),
			'status' => $request->input('status')
		]);

		$response->sendJson([
			'status' => 'OK',
			'message' => 'Account created Successfully!',
			'id' => $id
		], 200);

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
	/*
	public function updateAccount(Request $request, Response $response, array $middlewareData) : bool {

		$userId = $request->paths(1);

		$validation = Validator::validateDataStructure(
			[
				'userId' => $userId
			],
			[
				'userId' => 'id'
			]
		);

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 404);
			return false;
		}

		$userModel = new AccountModel();

		$userRecord = $userModel->fetchRow([
			'userId' => $userId
		]);

		# In case if account not found
		if(!$userRecord) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Account not found'
			], 404);

			return false;
		}

		# Let's check the user Status if Terminated
		if($userRecord['status'] == USER_STATUS_TERMINATED) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Account terminated'
			], 404);

			return false;
		}

		# Validate User Data
		$validation = Validator::validateJson(
			$request->rawInput(),
			[
				'firstName' => 'string_length:2:15',
				'lastName' => 'string_length:2:20',
				'email' => 'email',
				'password' => 'password:6:256',
				'phone' => 'string_length:6:20',
				'comment' => 'string_length:0:255:!required',
				// @todo figure out this
                //'roleId' => 'one_of:'.USER_ROLE_SERVICE_PROVIDER.':'.USER_ROLE_CLIENT.':'.USER_ROLE_ADMIN.':'.USER_ROLE_SUPER_ADMIN.':'.USER_ROLE_DEVELOPER,
				'status' => 'one_of:'.USER_STATUS_ACTIVE.':'.USER_STATUS_NOT_VERIFIED
			],
			[
				'general' => 'exact_keys_values'
			]
		);

		# Check if email address is valid then try to check if exists in db.
		if(!isset($validation['email'])) {

			# Email entered valid. Then try to check if exists in db.
			if($userModel->emailExistsById($request->input('email'), $userId) == true) {
				$validation['email'] = ['Email address is already registered'];
			}

		}

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		# Update User account
		$userModel->updateRow([
			'firstName' => $request->input('firstName'),
			'lastName' => $request->input('lastName'),
			'email' => $request->input('email'),
			'password' => $request->input('password'),
			'phone' => $request->input('phone'),
			'comment' => $request->input('comment'),
			'roleId' => $request->input('roleId'),
			'status' => $request->input('status')
		], [
			'userId' => $userId,
			'email' => $userRecord['email']
		]);

		$response->sendJson([
			'status' => 'OK',
			'message' => 'Account Updated successfully'
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

		$userId = $request->paths(1);

		$validation = Validator::validateDataStructure(
			[
				'userId' => $userId
			],
			[
				'userId' => 'id'
			]
		);

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 404);
			return false;
		}

		$userModel = new AccountModel();

		$userRecord = $userModel->fetchRow([
			'userId' => $userId
		]);

		# In case if account not found
		if(!$userRecord) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Account not found'
			], 404);

			return false;
		}

		# Let's check the user Status if Terminated
		if($userRecord['status'] == USER_STATUS_TERMINATED) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Account terminated'
			], 404);

			return false;
		}

		# Validate User Data
		$validation = Validator::validateJson(
			$request->rawInput(),
			[
				'firstName' => 'string_length:2:15:!required',
				'lastName' => 'string_length:2:20:!required',
				'email' => 'email:!required',
				'password' => 'password:6:256:!required',
				'phone' => 'string_length:6:20:!required',
				'comment' => 'string_length:0:255:!required',
                // @todo figure out this
				//'roleId' => 'one_of:'.USER_ROLE_SERVICE_PROVIDER.':'.USER_ROLE_CLIENT.':'.USER_ROLE_ADMIN.':'.USER_ROLE_SUPER_ADMIN.':'.USER_ROLE_DEVELOPER.':!required',
				'status' => 'one_of:'.USER_STATUS_ACTIVE.':'.USER_STATUS_NOT_VERIFIED.':'.USER_STATUS_SUSPENDED.':!required'
			],
			[
				'general' => 'at_least_one_value|no_extra_values'
			]
		);

		# If there is email specified, let's check if not registered.
		if(!empty($request->input('email'))) {
			if($userModel->emailExistsById($request->input('email'), $userId) == true) {
				$validation['email'] = ['Email address is already registered'];
			}
		}

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$allowedFields = [
			'firstName',
			'lastName',
			'email',
			'password',
			'phone',
			'comment',
			'roleId',
			'status'
		];

		$updateFields = [];

		foreach ($request->input() as $field => $value) {
			if(in_array($field, $allowedFields)) {
				$updateFields[$field] = $value;
			}
		}

		# Update User account
		$userModel->updateRow($updateFields, [
			'userId' => $userId,
			'email' => $userRecord['email']
		]);

		$response->sendJson([
			'status' => 'OK',
			'message' => 'Account Patched successfully'
		], 200);

		return true;
	}

	/**
	 * Terminate user account with `DELETE` Request
	 *
	 * @access public 
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @throws \Exception
	 * @return bool
	 */
	public function delete(Request $request, Response $response, array $middlewareData) : bool {

		$userId = $request->paths(1);

		$validation = Validator::validateDataStructure(
			[
				'userId' => $userId
			],
			[
				'userId' => 'id'
			]
		);

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 404);
			return false;
		}

		# Let's check, if this is own account
		if((int) $userId === (int) $middlewareData['account']['userId']) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'You are not able to terminate your own account!'
			], 422);

			return false;
		}

		$userModel = new AccountModel();

		$userRecord = $userModel->fetchRow([
			'userId' => $userId
		]);

		# In case if account not found
		if(!$userRecord) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Account not found'
			], 404);

			return false;
		}

		$updateFields = [
			'status' => USER_STATUS_TERMINATED,
			'dateLastUpdate' => date('Y-m-d H:i:s')
		];

		# Update User account
		$userModel->updateRow($updateFields, [
			'userId' => $userId,
			'email' => $userRecord['email']
		]);

		$response->sendJson([
			'status' => 'OK',
			'message' => 'Account Terminated successfully',
		], 200);

		return true;

	}

}
