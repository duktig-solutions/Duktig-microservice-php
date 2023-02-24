<?php
/**
 * Get User location by IP from given resources
 * 
 * This library using `IPTolocation` directive from application configuration.
 *
 * @version 1.0.0
 */
namespace Lib;

use System\Config;
use Lib\HttpClient;

class LocationByIP {

    public static function fetch(string $ipAddress) {

        # Debug
        # $ipAddress = '37.252.92.164'; // Armenia
        # $ipAddress = '68.171.212.74'; // United States

        # Case if IP Address from local network 
        if(substr($ipAddress, 0, 8) == '192.168.') {
            return 'Local Network';
        }

        $resource = Config::get('app')['IPTolocation']['resource'];        
                        
        $response = HttpClient::sendRequest($resource . $ipAddress, 'GET');

        if($response['error']) {
            return 'N/A';
        }

        $data = json_decode($response['result'], true);

        return isset($data['country']) ? $data['country'] : 'N/A';
        
    }

}