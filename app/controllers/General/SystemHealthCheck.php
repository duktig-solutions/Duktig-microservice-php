<?php
/**
 * System Health Check Controller
 *
 * - Just Ping and get response plain text: pong
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\General;

use System\Request;
use System\Response;

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
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return void
	 */
	public function ping(Request $request, Response $response, array $middlewareData) : void {
		$response->status(200);
		$response->write('pong');
	}

}