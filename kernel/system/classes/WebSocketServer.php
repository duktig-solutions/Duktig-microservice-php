<?php
/**
 * Web Socket Server
 *
 * @todo Finalize this
 */
namespace System;

use \System\WebSocketRouter as Router;

class WebSocketServer {

	# Instance Name
	private $instanceName;

	# Instance Configuration
	private $config;

	//////////////////////////////////////
	private $socketResource;

	private $clientResources = [];
	private $connectedClients = [];

	/**
	 * WebSocketServer constructor.
	 *
	 * @param array $config
	 * @throws \Exception
	 */
	public function __construct($config) {

		# Initialize WebSocket Server Instance Configuration
		$this->config = $config;

	}

	public function listen() {

		$this->socketResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		socket_set_option($this->socketResource, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($this->socketResource, 0, $this->config['port']);
		//socket_set_nonblock($this->socketResource);
		socket_listen($this->socketResource);

		$this->clientResources = [$this->socketResource];

		$this->run();

	}

	private function run() {

		$null = null;

		while (true) {

			$client_ip_address = '';

			$newSocketArray = $this->clientResources;
			socket_select($newSocketArray, $null, $null, 0, 10);

			// New Connection
			if (in_array($this->socketResource, $newSocketArray)) {

				$newSocket = socket_accept($this->socketResource);
				$this->clientResources[] = $newSocket;

				$header = socket_read($newSocket, 1024);

				$this->handshake($header, $newSocket);

				socket_getpeername($newSocket, $client_ip_address);

				//$connectionACK = $chatHandler->seal(json_encode(['message'=>'Welcome!','message_type'=>'chat-connection-ack'])); // newConnectionACK($client_ip_address);
				//$chatHandler->send($connectionACK);

				$clientId = $this->hashClient($newSocket);

				$socketClients[$clientId] = ['clientId' => $clientId, 'auth' => 0];

				// Ask to auth
				$connectionACK = $this->seal(json_encode(['message' => 'You Connected!', 'message_type' => 'auth-required', 'clientId' => $clientId])); // newConnectionACK($client_ip_address);
				$this->send($connectionACK, $clientId);

				// Remove this new connection from temporary array
				$newSocketIndex = array_search($this->socketResource, $newSocketArray);
				unset($newSocketArray[$newSocketIndex]);

				// New Connection
				echo "Connected: " . count($this->clientResources) . "\n";

			}

			foreach ($newSocketArray as $newSocketArrayResource) {

				$socketData = '';
				$socketDataTotal = '';

				while (($bytes = socket_recv($newSocketArrayResource, $socketData, 4000, 0)) >= 1) {

					if($bytes === false) {
						echo " ~~~ NO BYTES NO DATA !!!\n";
						break 2;
					}

					if($bytes === 0) {
						echo " ~~~ Client Disconnected !!!\n";
						break 2;
					}

					if(!$socketData) {
						echo "No read_conv!\n";
						break 2;
					}

					// Get message
					$socketDataTotal .= $socketData;

					$socketMessage = $this->unseal($socketData);
					$messageObj = json_decode($socketMessage);

					$clientId = $this->hashClient($newSocketArrayResource);

					if(!isset($messageObj->chat_message)) {

						//$msg = $this->seal(json_encode(['message' => "Message: Some message" . "<br>", 'message_type' => 'chat-box-html']));
						echo "Chat_message is empty!\n";
						var_dump($socketData);
						var_dump($socketMessage);
						echo "Bytes: " . $bytes. "\n";

						break 2;

					} else {
						$msg = $this->seal(json_encode(['message' => "Message: " . $messageObj->chat_message . "<br>", 'message_type' => 'chat-box-html']));
					}

					$this->send($msg, $clientId);

					echo "Clients when messaging (".count($this->clientResources).")\n";
					echo $socketMessage . "\n";

					//echo 'Mem: ' . ByteToMem(memory_get_usage()) . "\n";

					usleep(200);

					break 2;

				}


				if($socketData) {
					echo "OK";
				}

				if(socket_last_error($newSocketArrayResource) > 0) {
					echo "@@@ Now I know !!!\n";
					echo socket_strerror(socket_last_error($newSocketArrayResource)) . ' : '.socket_last_error($newSocketArrayResource) . "\n";
				}

				try {

					$buff = '';
					$bytes = socket_recv($newSocketArrayResource, $buff, 1024, 0);

					if($bytes === false) {
						echo " ~~~ # NO BYTES NO DATA !!!\n";
						continue;
					}

					if($bytes === 0) {
						echo " ~~~ # Client Disconnected !!!\n";

						socket_close($newSocketArrayResource);

						$newSocketIndex = array_search($newSocketArrayResource, $this->clientResources);
						unset($this->clientResources[$newSocketIndex]);

						$newSocketIndex = array_search($newSocketArrayResource, $newSocketArray);
						unset($newSocketArray[$newSocketIndex]);

						echo "Client Disconnected! (".count($this->clientResources).")\n";

						continue;
					}

					//$socketDataTest = socket_read($newSocketArrayResource, 1024, PHP_NORMAL_READ);
				} catch (\Throwable $e) {
					echo "CANNOT READ SOCKET !!!\n";
					$socketDataTest = false;
				}

				/*
				if(!$socketDataTest) {
					echo "Socket Read Issue:\n";
					var_dump($socketDataTest);
				}

				if ($socketDataTest === false) {
					//$client_ip_address  = '';
					//socket_getpeername($newSocketArrayResource, $client_ip_address);

					// No need to notice about disconnect
					//$connectionACK = $chatHandler->connectionDisconnectACK($client_ip_address);
					//$chatHandler->send($connectionACK);

					$newSocketIndex = array_search($newSocketArrayResource, $this->clientResources);
					unset($this->clientResources[$newSocketIndex]);

					$newSocketIndex = array_search($newSocketArrayResource, $newSocketArray);
					unset($newSocketArray[$newSocketIndex]);

					echo "Client Disconnected! (".count($this->clientResources).")\n";
					echo "Client Disconnected! (".count($newSocketArray).")\n";

				}
				*/

			}
		}

	}

	private function handshake($received_header, $client_socket_resource) {

		$headers = array();
		$lines = preg_split("/\r\n/", $received_header);

		foreach($lines as $line) {

			$line = chop($line);

			if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)) 	{
				$headers[$matches[1]] = $matches[2];
			}
		}

		$secKey = $headers['Sec-WebSocket-Key'];
		$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

		$buffer  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
			"Upgrade: websocket\r\n" .
			"Connection: Upgrade\r\n" .
			"WebSocket-Origin: ".$this->config['hostname']."\r\n" .
			"WebSocket-Location: ws://".$this->config['hostname'].":".$this->config['port']."\r\n".
			"Sec-WebSocket-Accept:$secAccept\r\n\r\n";

		socket_write($client_socket_resource,$buffer,mb_strlen($buffer));
	}

	private function hashClient($socketResource) {

		if(!$socketResource) {
			return False;
		}

		return md5(str_replace(' ', '_', (string) $socketResource));

	}

	private function seal($socketData) {

		$b1 = 0x80 | (0x1 & 0x0f);
		$length = mb_strlen($socketData);

		if($length <= 125)
			$header = pack('CC', $b1, $length);
		elseif($length > 125 && $length < 65536)
			$header = pack('CCn', $b1, 126, $length);
		elseif($length >= 65536)
			$header = pack('CCNN', $b1, 127, $length);
		return $header.$socketData;
	}

	private function unseal($socketData) {
		$length = ord($socketData[1]) & 127;
		if($length == 126) {
			$masks = substr($socketData, 4, 4);
			$data = substr($socketData, 8);
		}
		elseif($length == 127) {
			$masks = substr($socketData, 10, 4);
			$data = substr($socketData, 14);
		}
		else {
			$masks = substr($socketData, 2, 4);
			$data = substr($socketData, 6);
		}
		$socketData = "";
		for ($i = 0; $i < mb_strlen($data); ++$i) {
			$socketData .= $data[$i] ^ $masks[$i%4];
		}
		return $socketData;
	}

	private function send($message, $toClient = NULL) {

		$messageLength = mb_strlen($message);

		// Send to all
		if(is_null($toClient)) {
			foreach ($this->clientResources as $clientSocket) {
				@socket_write($clientSocket, $message, $messageLength);
			}

			return True;
		}

		foreach ($this->clientResources as $clientSocket) {
			if($this->hashClient($clientSocket) == $toClient) {
				@socket_write($clientSocket, $message, $messageLength);
			}
		}

		return true;

	}

	private function getClientIdByUserIdStr($userIdStr) {

		foreach ($this->connectedClients as $socketClient) {
			if($socketClient['userIdStr'] == $userIdStr) {
				return $socketClient['clientId'];
			}
		}

		return False;
	}

	public function __destruct() {
		socket_close($this->socketResource);
	}


}