<?php
/**
 * Middleware class for Web Socket Server Auth
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Auth;

class WebSocket {

	/**
	 * just inject some middleware Data
	 *
	 * @param array $middlewareData
	 * @return array
	 */
	public function justInfoInjected(array $middlewareData) : array {
		$middlewareData['dataInjected'] = time();

		return $middlewareData;
	}

	public function authByClientKey(array $middlewareData) {

	}
}
