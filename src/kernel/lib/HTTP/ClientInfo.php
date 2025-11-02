<?php
/**
 * Get Client information from request
 *   
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 */
namespace Lib\HTTP;

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

        if (isset($_SERVER['X_REAL_IP'])) {
            return $_SERVER['X_REAL_IP'];  // From Nginx X-Real-IP header
        } elseif (isset($_SERVER['X_FORWARDED_FOR'])) {
            return strtok($_SERVER['X_FORWARDED_FOR'], ',');  // First IP in the list
        }

        if(getenv('HTTP_X_REAL_IP')) {
            return getenv('HTTP_X_REAL_IP');
        } elseif(getenv('HTTP_CLIENT_IP')) {
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