<?php
/**
 * Data Reception General Receiver controller
 */

namespace App\Controllers\DataReception;

use System\Response;
use System\Request;
use \Lib\Validator;
use App\Models\DataReception\DataReception;

class DataReceiverGeneral {

	/**
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function receive(Request $request, Response $response, array $middlewareData) : bool {

		# Validate Data
		$validation = Validator::validateDataStructure(
			['body' => $request->rawInput()],
			[
				'body' => 'required'
			]
		);

		// There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$model = new DataReception(\System\Config::get()['Databases']['dataReception']);

		$model->insertDataReceived([
			'dataReceived' => $request->rawInput(),
			'dateReceived' => date('Y-m-d H:i:s'),
			'status' => 'pending',
			'systemId' => $middlewareData['systemId']
		]);

		$response->sendJson([
			'status' => 'ok',
			'message' => 'Data Received Successfully'
		], 200);

		return true;

	}

}