<?php
/**
 * Permissions Controller
 * This Controller assumes to get Permissions of all Microservices from Intermediate Data Center.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Accounts\AccountsManagement;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class Permissions
 *
 * @package App\Controllers
 */
class Permissions {

	/**
	 * Get All Permissions of all Microservices
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
			['permissions' => 'Coming soon']
		);

		return true;

	}
	
}
