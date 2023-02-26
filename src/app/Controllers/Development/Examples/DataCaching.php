<?php
/**
 * Example of Data caching by middleware class via Redis.
 */
namespace App\Controllers\Development\Examples;

use Exception;
use System\HTTP\Request;
use System\HTTP\Response;
use System\CLI\Input;
use System\CLI\Output;
use System\Config;
use Lib\Validator;
use Lib\Cache\Redis as CacheClient;

class DataCaching {

	private CacheClient $cacheLib;

	public function __construct() {

		# Load cache library
		$this->cacheLib = new CacheClient(Config::get()['Redis']['SystemCaching']);
	}

	/**
	 * System cached functionality
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws Exception
	 */
	public function httpTestSystemCaching(Request $request, Response $response, array $middlewareData) : bool {

		# Let's Validate the GET Request data
		$validation = Validator::validateDataStructure(
			$request->get(),
			[
				'offset' => 'int_range:0:1000'
			]
		);

		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return False;
		}

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

	/**
	 * Custom cached functionality
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws Exception
	 */
	public function httpTestManualCaching(Request $request, Response $response, array $middlewareData) : bool {

		# Let's Validate the GET Request data
		$validation = Validator::validateDataStructure(
			$request->get(),
			[
				'offset' => 'int_range:0:1000'
			]
		);

		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return False;
		}

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
		$this->cacheLib->setArray(md5($request->uri()), $data, 10); // 3 seconds

		return True;

	}

	public function cliTestCaching(Input $input, Output $output) {
    
        $key = 'hwd';
        $value = 'Hello World!';
        $expireInSeconds = 3;

        $output->stdout('Testing Redis cache functionality');
        $output->stdout('Setting value "'.$value.'" with key "'.$key.'" to Redis cache which will be expired in '.$expireInSeconds.' seconds');

        $this->cacheLib->set($key, $value, $expireInSeconds);

        $output->stdout('Getting value from Redis Cache');

        $cachedValue = $this->cacheLib->get($key);

        $output->stdout($cachedValue);

        $output->stdout('OK! waiting to expire...');
        sleep(5);

        $output->stdout('Now, after 5 seconds this should be expired.');
        $cachedValue = $this->cacheLib->get($key);

        $output->stdout('See the var_dump() of expired value.');
        var_dump($cachedValue);

        $output->stdout('Done!');
    }

}