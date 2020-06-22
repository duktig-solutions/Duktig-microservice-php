<?php
/**
 * Middleware to detect and insert user actions into database
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware;

use System\Request;
use System\Response;
use System\Input;
use System\Output;

/**
 * Class UserActions
 *
 * @package App\Middleware
 */
class UserActions {

	public function cliAction(Input $input, Output $output, array $middlewareData) {

		$userActionsModel = new \App\Models\UserActions();
		$userActionsModel->insertAction(
			USER_ID_SYSTEM,
			json_encode($input->args()),
			\System\CliRouter::getRoute()['controller']
		);

		$middlewareData['userActionInserted'] = true;

		return $middlewareData;
	}

	public function httpAction(Request $request, Response $response, array $middlewareData) {

		$userActionsModel = new \App\Models\UserActions();

		# Let's remove a password from request data if exists.
		$tmpMessage = $request->input();

		if(isset($tmpMessage['password'])) {
			$tmpMessage['password'] = '{removed}';
		}

		$message = json_encode($tmpMessage);

		if(isset($middlewareData['account']['userId'])) {
			$userId = $middlewareData['account']['userId'];
		} else {
			$userId = USER_ID_GUEST;
		}

		$userActionsModel->insertAction(
			$userId,
			$message,
			\System\HttpRouter::getRoute()['controller']
		);

		$middlewareData['userActionInserted'] = true;

		return $middlewareData;

	}

}