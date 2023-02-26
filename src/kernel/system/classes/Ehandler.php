<?php
/**
 * Base Error Handler Class for systems
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

/**
 * Class Ehandler
 *
 * @package Kernel\System\Classes
 */
class Ehandler extends \Exception implements \Throwable {

	/**
     * Return Error group by code
     *
     * @static
     * @access protected
     * @param int $errCode
     * @return string
     */
    protected static function getErrorGroupName(int $errCode) : string {

        switch($errCode) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $errGroup = Logger::INFO;
                break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $errGroup = Logger::WARNING;
                break;
            case E_ERROR:
            case E_USER_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_PARSE:
            case E_STRICT:
                $errGroup = Logger::ERROR;
                break;
            case 0:
                $errGroup = Logger::EXCEPTION;
                break;
            default:
                $errGroup = Logger::UNKNOWN;
                break;
        } // end switch

        return $errGroup;

	}

    /**
     * Process an error
     *
     * @static
     * @access public
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @return bool
     */
	public static function processError(string $message, int $code, string $file, int $line) : bool {

		$type = self::getErrorGroupName($code);

		// Do not Log Error if not enabled
		if(Config::get()['LogErrors']) {
			Logger::Log($message, $type, $file, $line);
		}

		if($type == Logger::INFO) {
			return false;
		}

		return true;

	}

    /**
     * Get detailed information about error
     *
     * @access public
     * @param string $message
     * @param int $code
     * @return array
     */
	public static function getDetailed(string $message, int $code) : array {

		$type = self::getErrorGroupName($code);

		$message = $type . ' | ' . $message;

		// Let's hide Actual Error information in production mode.
		if(Config::get()['Mode'] == 'production') {

			$data = [
				'status' => 'error',
				'message' => 'Internal Server Error'
			];

		} else {

			$data = [
				'status' => 'error',
				'type' => $type,
				'message' => $message
			];
		}

		return $data;

	}

}
