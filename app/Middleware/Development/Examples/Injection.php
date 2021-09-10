<?php
/**
 * Example of Data Injection in Middleware class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Development\Examples;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class Injection
 *
 * @package App\Middleware
 */
class Injection {

	public function injectMiddlewareData(Request $request, Response $response, array $middlewareData) {

		$middlewareData['GET_Request_count'] = count($request->get());
		$middlewareData['Some_other_data'] = 'This is a message injected in Middleware class method.';

		return $middlewareData;

	}

}
