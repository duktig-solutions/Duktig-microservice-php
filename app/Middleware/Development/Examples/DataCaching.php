<?php
/**
 * DataCaching middleware class to handle Response data caching.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Development\Examples;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;
use Lib\Cache\Redis as CacheClient;

/**
 * Class DataCaching
 *
 * @package App\Middleware
 */
class DataCaching {

	public function responseFromCache(Request $request, Response $response, array $middlewareData) {

		$cacheLib = new CacheClient(Config::get()['Redis']['DevelopmentTestSystemCaching']);

		$key = md5($request->uri());

		$content = $cacheLib->getArray($key);

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