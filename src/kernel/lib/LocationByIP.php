<?php
/**
 * Get User location by IP from given resources
 * 
 * This library uses `IPTolocation` directive from application configuration.
 *
 * @version 1.0.0
 */
namespace Lib;

use \Lib\HTTP\Client;
use System\Config;

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
                        
        $response = Client::sendRequest($resource . $ipAddress, 'GET');

        if($response['error']) {
            return 'N/A';
        }

        $data = json_decode($response['result'], true);

        return $data['country'] ?? 'N/A';
        
    }

}