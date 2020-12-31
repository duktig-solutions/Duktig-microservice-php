<?php


define('HOST_NAME',"localhost");
define('PORT',"8090");
$null = NULL;

require_once("class.chathandler.php");
$chatHandler = new ChatHandler();

$socketResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_set_option($socketResource, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socketResource, 0, PORT);
socket_listen($socketResource);

$client_ip_address = 'asdasds';

$clientSocketArray = [$socketResource];
/*
	'__SERVER__' => [
		'resource' => ]
];
*/

$socketClients = [
	/*
	{userIdStr} => [
		'id' => {userIdStr},
		'auth' => false by default,
	]
	*/
];

echo 'Mem: ' . ByteToMem(memory_get_usage()) . "\n";

while (true) {

	$newSocketArray = $clientSocketArray;
	socket_select($newSocketArray, $null, $null, 0, 10);

	// New Connection
	if (in_array($socketResource, $newSocketArray)) {
		$newSocket = socket_accept($socketResource);
		$clientSocketArray[] = $newSocket;

		$header = socket_read($newSocket, 1024);

		$chatHandler->doHandshake($header, $newSocket, HOST_NAME, PORT);

		socket_getpeername($newSocket, $client_ip_address);

		//$connectionACK = $chatHandler->seal(json_encode(['message'=>'Welcome!','message_type'=>'chat-connection-ack'])); // newConnectionACK($client_ip_address);
		//$chatHandler->send($connectionACK);

		$clientId = hashClient($newSocket);

		$socketClients[$clientId] = [
			'clientId' => $clientId,
			'auth' => 0
		];

		// Ask to auth
		$connectionACK = $chatHandler->seal(json_encode(['message'=>'You Connected!','message_type'=>'auth-required', 'clientId' => $clientId])); // newConnectionACK($client_ip_address);
		$chatHandler->send($connectionACK, $clientId);

		// New Connection
		echo "\nFirst: " . $connectionACK . "\n";

		// Remove this new connection from temporary array
		$newSocketIndex = array_search($socketResource, $newSocketArray);
		unset($newSocketArray[$newSocketIndex]);


	}

	foreach ($newSocketArray as $newSocketArrayResource) {
		while(socket_recv($newSocketArrayResource, $socketData, 1024, 0) >= 1) {

			// Get message
			$socketMessage = $chatHandler->unseal($socketData);
			$messageObj = json_decode($socketMessage);

			$clientId =	$messageObj->clientId;

			if($messageObj->messageType == 'Auth') {

				// Check Auth by token !
				// If so:

				$socketClients[$clientId] = ['clientId' => $clientId, 'auth' => 1, 'userIdStr' => $messageObj->userIdStr];

			} elseif($messageObj->messageType == 'toPerson') {

				$personId = getClientIdByUserIdStr($messageObj->toUserIdStr);

				if($personId) {
					$msg = $chatHandler->seal(json_encode(['message' => "Message: " . $messageObj->chat_message . "<br>", 'message_type' => 'chat-box-html']));
					$chatHandler->send($msg, $personId);

					$msg = $chatHandler->seal(json_encode(['message' => "Message: " . $messageObj->chat_message . "<br>", 'message_type' => 'chat-box-html']));
					$chatHandler->send($msg, $clientId);
				} else {
					$msg = $chatHandler->seal(json_encode(['message' => "Not online!" . $messageObj->chat_user . "<br>", 'message_type' => 'chat-box-html']));
					$chatHandler->send($msg, $clientId);
				}

				//$msg = $chatHandler->seal(json_encode(['message' => "Got you ! " . $messageObj->chat_user . "<br>", 'message_type' => 'chat-box-html']));


			} else {

				// Now check, if user authorized
				if(!isset($socketClients[$clientId])) {
					$connectionACK = $chatHandler->seal(json_encode(['message'=>'You Connected!','message_type'=>'auth-required', 'clientId' => $clientId])); // newConnectionACK($client_ip_address);
					$chatHandler->send($connectionACK, $clientId);
				} elseif(!$socketClients[$clientId]['auth']) {
					$connectionACK = $chatHandler->seal(json_encode(['message' => 'You Connected!', 'message_type' => 'auth-required', 'clientId' => $clientId])); // newConnectionACK($client_ip_address);
					$chatHandler->send($connectionACK, $clientId);
				} else {

					// Send a response message
					$msg = $chatHandler->seal(json_encode(['message' => "Received your message! " . mt_rand(10, 100) . ' / ' . $messageObj->chat_user . "<br>", 'message_type' => 'chat-box-html']));
					$chatHandler->send($msg, $clientId);
				}

			}

			print_r($socketClients);

			echo 'Mem: ' . ByteToMem(memory_get_usage()) . "\n";

			break 2;
		}

		$socketData = @socket_read($newSocketArrayResource, 1024, PHP_NORMAL_READ);
		if ($socketData === false) {
			socket_getpeername($newSocketArrayResource, $client_ip_address);

			// No need to notice about disconnect
			//$connectionACK = $chatHandler->connectionDisconnectACK($client_ip_address);
			//$chatHandler->send($connectionACK);

			$newSocketIndex = array_search($newSocketArrayResource, $clientSocketArray);
			unset($clientSocketArray[$newSocketIndex]);
		}
	}
}
socket_close($socketResource);

function hashClient($socketResource) {

	if(!$socketResource) {
		return False;
	}

	return md5(str_replace(' ', '_', (string) $socketResource));

}

function getClientIdByUserIdStr($userIdStr) {

	global $socketClients;

	foreach ($socketClients as $socketClient) {
		if($socketClient['userIdStr'] == $userIdStr) {
			return $socketClient['clientId'];
		}
	}

	return False;
}

function ByteToMem($bytes) {

	if($bytes <= 0) {
		return '0 bytes';
	}

	$i = floor(log($bytes) / log(1024));
	$sizes = array('bytes', 'Kb', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

	return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
}

?>