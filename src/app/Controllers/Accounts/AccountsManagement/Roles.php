<?php
/**
 * Roles Management
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Accounts\AccountsManagement;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class Roles
 *
 * @package App\Controllers
 */
class Roles {

	/**
	 * Get All Roles
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
     * @todo finalize this
	 */
	public function getAll(Request $request, Response $response, array $middlewareData) : bool {

		$response->sendJson(
			['roles' => 'Coming soon']
		);

		return true;

	}

    /**
	 * Get Role by Id
	 *
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
     * @todo finalize this
	 */
	public function getById(Request $request, Response $response, array $middlewareData) : bool {

        $roleId = $request->paths(1);
        
		$response->sendJson(
			['role_data' => $roleId]
		);

		return true;

	}

	// @todo finalize this
	public function create(Request $request, Response $response, array $middlewareData) : bool {

		$response->sendJson(
			['role_create' => 'under maintenance']
		);

		return true;

	}

	// @todo finalize this
	public function patch(Request $request, Response $response, array $middlewareData) : bool {

		$response->sendJson(
			['role_patch' => 'under maintenance']
		);

		return true;

	}

	// @todo finalize this
	public function delete(Request $request, Response $response, array $middlewareData) : bool {

		$response->sendJson(
			['role_delete' => 'under maintenance']
		);

		return true;

	}
	
}
