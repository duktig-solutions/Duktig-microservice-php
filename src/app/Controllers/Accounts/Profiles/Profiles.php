<?php
/**
 * Get Accounts data as Profiles list.
 * The case when loading comments from "Comments" microservice and need to get users information by userIds
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Accounts\Profiles;

use Lib\Validator;
use System\HTTP\Request;
use System\HTTP\Response;
use App\Models\Accounts\Profiles\Profiles as ProfilesModel;

/**
 * Class Profiles
 *
 * @package App\Controllers
 */
class Profiles {

	/**
	 * Get User Accounts
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function getAllByIds(Request $request, Response $response, array $middlewareData) : bool {

		$ids = $request->get('ids');
		
		$validation = Validator::validateDataStructure(
			$request->get(),
			[
				'ids' => 'required'
			]
		);

		# There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}
        
        $a_ids = explode(',', $ids);

		$usersModel = new ProfilesModel();

		$response->sendJson(
			$usersModel->fetchAllByIds($a_ids)
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
	public function getById(Request $request, Response $response, array $middlewareData) : bool {

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

		$usersModel = new ProfilesModel();

		$userRecord = $usersModel->fetchById($userId);

		# In case if account not found
		if(!$userRecord) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Profile not found'
			], 404);

			return false;
		}

		$response->sendJson($userRecord, 200);

		return true;
	}

}
