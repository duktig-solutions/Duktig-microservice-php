<?php
/**
 * Data Reception Authentication middleware class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\DataReception;

use System\Request;
use System\Response;

/**
 * Class Auth
 *
 * @package App\Middleware\DataReception
 */
class Auth {

	/**
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return array|bool
	 */
	public function AuthByKey(Request $request, Response $response, array $middlewareData) {

		# Get Auth config
		$config = \System\Config::get()['DataReception'];

		# Get Auth key from Headers
		$authKey = $request->headers($config['Auth']['DRAuthKey']);

		if($authKey == $config['Auth']['DRAuthKeyValue']) {
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

}