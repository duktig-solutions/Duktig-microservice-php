<?php
/**
 * WebSocket Server controller
 * This controller will receive a request from Command line and start the web socket server.
 * As a Testing server we running a chat instance.
 * It is possible to create as many socket servers as you want.
 *
 * Usage: php ~/www/duktig.microservice.1/cli/exec.php web-socket-server --server-config Chat
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\WebSocket;

use System\Input;
use System\Output;
use System\Config;
use System\Logger;

class WebSocketServer {

	/**
	 * Start WebSocket server
	 *
	 * @param \System\Input $input
	 * @param \System\Output $output
	 * @param array $middlewareResult
	 * @throws \Exception
	 */
	public function serve(Input $input, Output $output, array $middlewareResult) : void {

		$serverConfigName = $input->parsed('server-config');

		if (!$serverConfigName) {
			throw new \Exception('--server-config cli argument required!');
		}

		$config = Config::get()['WebSocketServer'][$serverConfigName];

		if (!$config) {
			throw new \Exception('Cannot find WebSocket server configuration by ' . $serverConfigName);
		}

		$output->stdout('Initializing WebSocket Server instance `'.$serverConfigName.'`` - '.$config['hostname'].':'.$config['port']);
		Logger::Log('Initializing WebSocket Server instance `'.$serverConfigName.'`` - '.$config['hostname'].':'.$config['port'], Logger::INFO);

		$server = new \System\WebSocket\WebSocketServer($config);
		$server->listen();

		exit();
	}
}