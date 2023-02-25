<?php
/**
 * Logger class
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

use System\Config;

class Logger  {

	/**
	 * Log types
	 */
	public const INFO = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR = 'ERROR';
	public const CRITICAL = 'CRITICAL';
	public const EXCEPTION = 'EXCEPTION';
	public const UNKNOWN = 'UNKNOWN';

	/**
     * Types
     *
	 * @static
	 * @access protected
	 * @var array
	 */
	protected static array $types = [
		"notice",
        "warning",
        "exception",
        "error",
        "NA",
        "unknown",
        "info",
	];

	/**
	 * Return log types
	 *
	 * @access public
	 * @return array
	 */
	public static function types() : array {
		return static::$types;
	}

    /**
     * Log a message with details into log file
     *
     * Format: {date time} _| {service name} _| {type} _| {message} _| {file} _| {line}
     * Example info without file, line: 2023-02-25 16:31:14 _| data.warehouse.units.aggregator _| INFO _| Data Warehouse Units aggregation success.
     * Example Critical: 2023-02-25 16:31:14 _| data.warehouse.units.aggregator _| CRITICAL _| Unable to configure Data Warehouse. _| /var/www/app/Controllers/DataWarehouse/Setup.php _| 458
     *
     * @static
     * @access public
     * @param string $message
     * @param string $type = 'INFO'
     * @param string|null $file
     * @param int|null $line
     * @param string|null $logFile
     * @return void
     */
    public static function Log(string $message, string $type = 'INFO', ?string $file = NULL, ?int $line = NULL, ?string $logFile = 'app.log') : void {

        $message = date('Y-m-d H:i:s') . " _| " .
	          Config::get()['Microservice'] . " _| " .
	          $type . " _| " .
	          $message;

        if(!is_null($file)) {
	        $message .= " _| " . $file;
        }

	    if(!is_null($line)) {
		    $message .= " _| " . $line;
	    }

	    $message .= "\n";

        $filePath = DUKTIG_APP_PATH . 'log/'.$logFile;

        file_put_contents($filePath, $message, FILE_APPEND);

    }

	/**
	 * Parse Log line and return array
	 *
	 * @access public
	 * @param string $line
	 * @return array
	 */
    public static function parseLogLine(string $line) : array {

	    $result = [
		    'date' => '',
		    'microservice' => '',
		    'type' => static::INFO,
		    'message' => $line,
		    'file' => '',
		    'line' => ''
	    ];

	    $pos = strpos($line, "_|");

	    if($pos === false) {
		    return $result;
	    }

	    $splitLine = explode('_|', $line);

	    $result['date'] = trim($splitLine[0]);

	    if(isset($splitLine[1])) {
		    $result['microservice'] = trim($splitLine[1]);
	    }

	    if(isset($splitLine[2])) {
		    $result['type'] = trim($splitLine[2]);
	    }

	    if(isset($splitLine[3])) {
		    $result['message'] = trim($splitLine[3]);
	    }

	    if(isset($splitLine[4])) {
		    $result['file'] = trim($splitLine[4]);
	    }

	    if(isset($splitLine[5])) {
		    $result['line'] = trim($splitLine[5]);
	    }

	    return $result;

    }
}
