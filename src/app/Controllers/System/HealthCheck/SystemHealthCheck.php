<?php
/**
 * System Health Check
 *
 * - Just Ping and get response plain text: pong
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\System\HealthCheck;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class SystemHealthCheck
 *
 * @package App\Controllers
 */
class SystemHealthCheck {

	/**
	 * Just ping the system and get plain text: pong
	 *
	 * @access public
	 * @param Request $request
	 * @param Response $response
	 * @param array $middlewareData
	 * @return void
	 */
	public function ping(Request $request, Response $response, array $middlewareData) : void {
		$response->status(200);
		$response->write('pong');
	}

}