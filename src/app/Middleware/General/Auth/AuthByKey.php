<?php
/**
 * Middleware class to validate access Key
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\General\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

/**
 * Class AuthByKey
 *
 * @package App\Middleware
 */
class AuthByKey {

	/**
	 * @param \System\HTTP\Request $request
	 * @param \System\HTTP\Response $response
	 * @param array $middlewareData
	 * @return array|bool
	 */
    public function check(Request $request, Response $response, array $middlewareData) {

        # Get Auth config
        $config = Config::get()['Auth'];

        # Get Auth key from Headers
        $authKey = $request->headers($config['AuthKey']);

        if($authKey == $config['AuthKeyValue']) {
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