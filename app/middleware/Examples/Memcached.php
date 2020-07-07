<?php
/**
 * Memcached middleware class to handle Response data caching.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Examples;

use System\Request;
use System\Response;

/**
 * Class Memcached
 *
 * @package App\Middleware
 */
class Memcached {

	public function cachedResponse(Request $request, Response $response, array $middlewareData) {

		$config = \System\Config::get()['ResponseDataCaching'];

		$cacheLib = new \Lib\Cache\Memcached($config);

		$key = md5($request->uri());

		$content = $cacheLib->get($key);

		if(!empty($content)) {

			$content['type'] = 'Cached';
			$content['message'] = 'This data comes from cache.';

			$response->sendJson($content);
			$response->sendFinal();

			//return False;
		}

		$middlewareData['dataCached'] = False;

		return $middlewareData;
	}

}