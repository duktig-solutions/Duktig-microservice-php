<?php

namespace App\Controllers\Development\Examples;

/**
 * User Information
 */

use System\HTTP\Request;
use System\HTTP\Response;
use Lib\HTTP\ClientInfo as IpDetector;
use System\Logger;

class ClientInfo {

    public function dump(Request $request, Response $response, array $middlewareData) : bool {

        if($request->get('key') != 'abc123') {
            $response->sendJson(['error' => 'Unauthorized'], 401);
            $response->sendFinal();
        }

        //\Lib\HttpClient::sendRequest('http://duktig.microservice/examples/client_info?key=abc123&client_machine=Macbook15PHP-HTTPClient');

        Logger::Log(
            'client_ip: ' . IpDetector::ipAddress() . ' | ' .
            'client_info: ' . $request->server('HTTP_USER_AGENT') . ' | ' .
            'client_machine: ' . $request->get('client_machine'), 
            Logger::INFO, null, null,
            'ClientInfo.txt'
        );

        $response->sendJson([
            'client_ip' => IpDetector::ipAddress(),
            'client_info' => $request->server('HTTP_USER_AGENT'),
            'client_machine' => $request->get('client_machine')
        ]);
        
        return true;

    }
}