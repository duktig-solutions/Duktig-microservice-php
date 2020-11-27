<?php
/**
 * WebSocket Authorization Controller
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Auth;

/**
 * Class WebSocket
 *
 * @package App\Controllers
 */
class WebSocket {

	public function onConnect(array $middlewareData) : string {

		return json_encode([
			'clientId' => $middlewareData['clientId'],
			'message' => 'auth-required'
		]);

	}

}
