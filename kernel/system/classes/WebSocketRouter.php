<?php
/**
 * Web Socket Router class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

use \System\Logger;

class WebSocketRouter {

	private static $routes;

	/**
	 * @param string $serverInstanceName
	 * @return bool
	 * @throws \Exception
	 */
	public static function init(string $serverInstanceName) : bool {

		# Set Route Configuration by Server instance name
		static::$routes = isset(Config::get('websocket_server-routes')[$serverInstanceName]) ? Config::get('websocket_server-routes')[$serverInstanceName] : Null;

		# Case when there are no any configuration for given server
		if(empty(static::$routes)) {
			throw new \Exception('There are no WebSocket server routes configured for Instance: ' . $serverInstanceName);
		}

		return True;

	}

	public static function onConnect($middlewareData) {

		if(empty(static::$routes['onConnect'])) {
			return False;
		}

		return static::triggerEvent(static::$routes['onConnect'], $middlewareData);

	}

	public static function onClose($middlewareData) {

		if(empty(static::$routes['onClose'])) {
			return False;
		}

		return static::triggerEvent(static::$routes['onClose'], $middlewareData);

	}

	public static function onMessage($middlewareData, $socketData) {
		return json_encode(['message' => 'HELLO ' . $middlewareData['clientId'] .' !', 'you_sent' => $socketData]);
	}

	private static function triggerEvent($routeConfig, $middlewareData) {

		# Middleware
		if(!empty($routeConfig['middleware'])) {
			foreach ($routeConfig['middleware'] as $middleware) {

				$middlewareConfig = explode('->', $middleware);

				$className = "\\App\\Middleware\\$middlewareConfig[0]";
				$methodName = $middlewareConfig[1];

				# Create new Middleware Object and execute
				$middlewareObject = new $className();
				$middlewareData = $middlewareObject->$methodName($middlewareData);

			}
		}

		# Controller
		$ctrlConfig = explode('->', $routeConfig['controller']);

		$className = "\\App\\Controllers\\$ctrlConfig[0]";
		$methodName = $ctrlConfig[1];

		# Create New Controller Object and execute
		$ctrlObject = new $className();
		return $ctrlObject->$methodName($middlewareData);

	}

}