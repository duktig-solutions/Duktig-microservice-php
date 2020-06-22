<?php
/**
 * Testing Data caching by middleware class via Memcached.
 */
namespace App\Controllers\Tests;

use System\Request;
use System\Response;

class DataCaching {

	private $cacheLib;

	public function __construct() {

		# This library instance will used only in function which does manual caching.
		$this->cacheLib = new \Lib\Cache\Memcached(\System\Config::get()['ResponseDataCaching']);
	}

	public function getCached(Request $request, Response $response, array $middlewareData) : bool {

		# Let's wait one second to demo delay.
		sleep(1);

		$content = range($request->get('offset'), $request->get('offset') + 20);

		$data = [
			'status' => 'ok',
			'message' => 'If this request responded in more than 1 second, it not comes from cache.',
			'offset' => $request->get('offset'),
			'data' => $content
		];

		# The response object will care about data caching.
		$response->sendJson($data);

		# As you see, in this method there are no any caching functionality.

		return True;
	}

	public function getCustomCached(Request $request, Response $response, array $middlewareData) : bool {

		# Let's wait one second to demo delay.
		sleep(1);

		$content = range($request->get('offset') * 10, $request->get('offset') + 20);

		$data = [
			'status' => 'ok',
			'type' => 'not cached',
			'message' => 'This data comes directly from controller and this is not cached. The caching functionality works in Middleware and Controller.',
			'offset' => $request->get('offset'),
			'data' => $content
		];

		# The response object will care about data caching.
		$response->sendJson($data);

		# We have to cache the content manually
		$this->cacheLib->set(md5($request->uri()), $data);

		return True;

	}

}