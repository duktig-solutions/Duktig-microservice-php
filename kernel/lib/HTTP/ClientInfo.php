<?php
/**
 * Get Client information from request
 *   
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0 
 */
namespace Lib\Http;

/**
 * class ClientInfo
 */
class ClientInfo {
    
    /**
     * Return Client IP Address
     *
     * @return string
     */
    public static function ipAddress() : string {

		if(getenv('HTTP_CLIENT_IP')) {
			return getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
			return getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('HTTP_X_FORWARDED')) {
			return getenv('HTTP_X_FORWARDED');
        } elseif(getenv('HTTP_FORWARDED_FOR')) {
			return getenv('HTTP_FORWARDED_FOR');
        } elseif(getenv('HTTP_FORWARDED')) {
			return getenv('HTTP_FORWARDED');
        } elseif(getenv('REMOTE_ADDR')) {
			return getenv('REMOTE_ADDR');
		}

        return '';

    }

    /**
     * Return User Agent in one string
     */
    public static function userAgent() : string {
        return getenv('HTTP_USER_AGENT');
    }

}