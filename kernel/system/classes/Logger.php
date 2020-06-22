<?php
/**
 * Logger class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

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
	 * @static
	 * @access protected
	 * @var array
	 */
	protected static $types = [
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
     * Logger method
     * Example format: 2019-11-02 13:08:19,687 _| DataCollector _| ERROR _| Cannot find configuration for collector: abc! _| Collector.py _| 61
     *
     * @static
     * @access public
     * @param string $message
     * @param string $type = 'INFO'
     * @param string $file
     * @param int $line
     * @param string $logFile
     * @return void
     */
    public static function Log(string $message, string $type = 'INFO', ?string $file = NULL, ?int $line = NULL, $logFile = 'app.log') : void {

        $message = date('Y-m-d H:i:s,v') . " _| " .
	          \System\Config::get()['SystemId'] . " _| " .
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

	    $result['date'] = $splitLine[0];

	    if(isset($splitLine[1])) {
		    $result['type'] = $splitLine[1];
	    }

	    if(isset($splitLine[2])) {
		    $result['message'] = $splitLine[2];
	    }

	    if(isset($splitLine[3])) {
		    $result['file'] = $splitLine[3];
	    }

	    if(isset($splitLine[4])) {
		    $result['line'] = $splitLine[4];
	    }

	    return $result;

    }
}
