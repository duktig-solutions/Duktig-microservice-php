<?php
/**
 * Base Error Handler Class for systems
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.2
 */
namespace System;

use Exception;
use Throwable;

/**
 * Class Ehandler
 *
 * @package Kernel\System\Classes
 */
class Ehandler extends Exception implements Throwable {

	/**
     * Return Error group by code
     *
     * @static
     * @access protected
     * @param int $errCode
     * @return string
     */
    protected static function getErrorGroupName(int $errCode) : string {

        return match ($errCode) {
            E_NOTICE, E_USER_NOTICE => Logger::INFO,
            E_WARNING, E_USER_WARNING, E_CORE_WARNING, E_COMPILE_WARNING => Logger::WARNING,
            E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR, E_PARSE => Logger::ERROR,
            0 => Logger::EXCEPTION,
            default => Logger::UNKNOWN,
        };

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
