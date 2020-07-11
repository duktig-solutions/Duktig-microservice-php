<?php
/**
 * Example Getter Controller:
 *
 * - File download
 * - Response with all request data
 * - Send Http request in CLI mode
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Examples;

use System\Request;
use System\Response;
use System\Input;
use System\Output;
use Lib\Benchmarking as bm;

/**
 * Class Getter
 *
 * @package App\Controllers\Examples
 */
class Getter {

	/**
	 * Download file
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return void
	 */
	public function downloadFile(Request $request, Response $response, array $middlewareData) : void {
		$response->sendFile(DUKTIG_ROOT_PATH . 'README.md');
	}

	/**
	 * Response with all request data
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 */
	public function responseAllRequestData(Request $request, Response $response, array $middlewareData) : bool {

		$response->sendJson([
			'status' => 'ok',
			'data' => [
				'MIDDLEWARE_DATA' => $middlewareData,
				'POST_FORM_DATA' => $request->input(),
				'POST_RAW_DATA' => $request->rawInput(),
				'URL_PATHS' => $request->paths(),
				'GET_DATA' => $request->get(),
				'HEADERS' => $request->headers()
			]
		], 200);

		//\System\Logger::Log($request->rawInput());
		\System\Logger::Log(print_r($request->Input(), true), \System\Logger::INFO);

		return true;

	}

	/**
	 * Send Http request from command line interface
	 *
	 * @access public
	 * @param \System\Input $input
	 * @param \System\Output $output
	 * @param array $middlewareResult
	 * @return void
	 */
	public function cliSendHttpRequest(Input $input, Output $output, array $middlewareResult) : void {

		bm::reset();

		# How many requests to send
		$limit = 1000;

		# Adding checkpoint start
		Bm::checkPoint('Start '.$limit.' requests');

		$developerKey = \System\Config::get()['AuthDevelopers']['DevAuthKeyValue'];

		# Send asynchronous http requests using curl
		$output->stdout('Sending Asynchronous Http Requests using curl:');

		Bm::checkPoint('Before async curl');

		for($i = 1; $i <= $limit; $i++) {

			# Send asynchronous http request using curl
			\Lib\HttpClient::sendRequestAsync(
				# URL
				'http://duktig.microservice/examples/response_all_request_data?a=1&b=2',
				# Request method
				'POST',
				# Data
				'{"Async_Val":"Example data","message":"Hello World!"}',
				# Headers
				[
					'X-Dev-Auth-Key' => $developerKey,
					//'Content-Type' => 'multipart/form-data',
					'Content-Type' => 'application/json'
				]
			);

		}

		Bm::checkPoint('After Async curl');

		# Sending http requests using php curl
		$output->stdout('Sending http requests using php curl:');

		Bm::checkPoint('Before php curl');

		for($i = 1; $i <= $limit; $i++) {

			$result = \Lib\HttpClient::sendRequest(
				# URL
				'http://duktig.microservice/examples/response_all_request_data?a=1&b=2',
				# Request method
				'POST',
				# Post data
				'{"PHP_VAL1":11,"PHP_VAL2":12}',
				//["PHP_postVar1" => 'Hello!', "PHP_postVar2" => 'World!'],

				# Headers
				[
					'X-Dev-Auth-Key' => $developerKey,
					//'Content-Type' => 'multipart/form-data',
					'Content-Type' => 'application/json'
				]
			);

		}

		Bm::checkPoint('After php curl');

		Bm::dumpResultsCli();

	}

}