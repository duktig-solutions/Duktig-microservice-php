<?php
/**
 * Getter Controller to test:
 *
 * - File download
 * - Response with all request data
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Tests;

use System\Request;
use System\Response;
use System\Input;
use System\Output;

/**
 * Class Getter
 *
 * @package App\Controllers\Tests
 */
class Getter {

	public function downloadFile(Request $request, Response $response, array $middlewareData) : void {
		$response->sendFile(DUKTIG_ROOT_PATH . 'README.md');
	}

	public function responseAllRequestData(Request $request, Response $response, array $middlewareData) : bool {

		$response->sendJson([
			'status' => 'ok',
			'data' => [
				'MIDDLEWARE_DATA' => $middlewareData,
				'POST_FORM_DATA' => $request->input(),
				'POST_RAW_DATA' => $request->rawInput(),
				'GET_DATA' => $request->get(),
				'HEADERS' => $request->headers()
			]
		], 200);

		//\System\Logger::Log($request->rawInput());
		\System\Logger::Log(print_r($request->Input(), true), \System\Logger::INFO);

		return true;

	}

	public function cliSendRequest(Input $input, Output $output, array $middlewareResult) {

		$limit = 1000;

		# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

		$output->stdout('CLI Curl:', false);

		$start = microtime(true);

		for($i = 1; $i <= $limit; $i++) {
			\Lib\HttpClient::sendRequestAsync(
				# URL
				'http://localhost/duktig.microservice.1/www/index.php/tests/response_all_request_data?a=1&b=2',
				# Request method
				'POST',
				# Data
				'{"Async_Val":"Testing","message":"Hello David!"}',
				//["AsyncPostVar1" => 'Hi', "AsyncPostVar2" => 'David!'],
				# Headers
				[
					'X-Dev-Auth-Key' => '8s79d#f798df9@78ds79f&8=79d',
					//'Content-Type' => 'multipart/form-data',
					'Content-Type' => 'application/json'
				]
			);
			//$output->stdout(microtime(true) - $start);
		}


		$time_elapsed_secs = microtime(true) - $start;

		$output->stdout($time_elapsed_secs);

		# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

		$output->stdout('PHP curl:', false);

		$start = microtime(true);

		for($i = 1; $i <= $limit; $i++) {
			// $result =
			$result = \Lib\HttpClient::sendRequest(
				# URL
				'http://localhost/duktig.microservice.1/www/index.php/tests/response_all_request_data?a=1&b=2',
				# Request method
				'POST',
				# Post data
				'{"PHP_VAL1":11,"PHP_VAL2":12}',
				//["PHP_postVar1" => 'Hello!', "PHP_postVar2" => 'World!'],

				# Headers
				[
					'X-Dev-Auth-Key' => '8s79d#f798df9@78ds79f&8=79d',
					//'Content-Type' => 'multipart/form-data',
					'Content-Type' => 'application/json'
				]
			);

			//print_r($result);
			//$output->stdout(microtime(true) - $start);
		}

		$time_elapsed_secs = microtime(true) - $start;

		//print_r($result);
		$output->stdout($time_elapsed_secs);

	}

}