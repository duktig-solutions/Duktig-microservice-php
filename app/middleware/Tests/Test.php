<?php
/**
 * Test Middleware for Development
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Tests;

use System\Request;
use System\Response;

/**
 * Class Test
 *
 * @package App\Middleware
 */
class Test {

	/**
	 * Authentication for Developer access by key
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return mixed
	 */
	public function AuthByDeveloperKey(Request $request, Response $response, array $middlewareData) {

		# Get Auth config
		$config = \System\Config::get()['AuthDevelopers'];

		# Get Auth key from Headers
		$authKey = $request->headers($config['DevAuthKey']);

		if($authKey == $config['DevAuthKeyValue']) {
			return $middlewareData;
		}

		$response->sendJson([
			'status' => 'error',
			'message' => 'Unauthorized'
		], 401);

		# Exit the application
		$response->sendFinal();

		return false;

	}

	public function injectMiddlewareData(Request $request, Response $response, array $middlewareData) {

		$middlewareData['GET_Request_count'] = count($request->get());
		$middlewareData['Some_other_data'] = 'This is message injected in Middleware class method.';

		return $middlewareData;

	}

}