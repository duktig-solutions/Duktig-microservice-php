<?php
/**
 * System detector middleware for data reception
 */

namespace App\Middleware\DataReception;

use System\Request;
use System\Response;

class SystemDetector {

	/**
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return array|bool
	 */
	public function detectByPaths(Request $request, Response $response, array $middlewareData) {

		$systemId = $request->paths(1);

		# Check if path is empty
		if(empty($systemId)) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Resource not found'
			], 404);

			return false;

		}

		# Check if SystemId Allowed
		$allowedSystemIds = \System\Config::get('http-routes')['POST']['/data-reception/{any}']['allowedSystemIds'];

		if(!in_array($systemId, $allowedSystemIds)) {

			$response->sendJson([
				'status' => 'error',
				'message' => 'Unknown System Id'
			], 422);

			return false;

		}

		# Inject path info Middleware data
		$middlewareData['systemId'] = $systemId;

		return $middlewareData;
	}

}