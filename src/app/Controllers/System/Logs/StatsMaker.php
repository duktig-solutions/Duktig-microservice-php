<?php
/**
 * Application logs statistics maker
 *
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 */

namespace App\Controllers\System\Logs;

use System\Logger;
use System\CLI\Input;
use System\CLI\Output;
use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class StatsMaker
 *
 * @package App\Controllers\General
 */
class StatsMaker {

	/**
	 * File size to move to archive
	 * Default 500kb
	 *
	 * @access private
	 * @var int
	 */
	private int $logFileSizeToArchive = 500000;

	/**
	 * Pattern to get log files (path with file type)
	 *
	 * @access private
	 * @var string
	 */
	private string $logFilesPathPattern;

	/**
	 * Log Files path
	 *
	 * @access private
	 * @var string
	 */
	private string $logFilesPath;

	/**
	 * AppLogs constructor.
	 */
	public function __construct() {

		# Initialize Log files path/pattern
		$this->logFilesPathPattern = DUKTIG_APP_PATH . 'log/*.log';
		$this->logFilesPath = DUKTIG_APP_PATH . 'log/';
	}

	/**
	 * CLI - Make Application log statistics
	 *
	 * @param Input $input
	 * @param Output $output
	 * @param array $middlewareResult
	 * @return bool
	 */
	public function process(Input $input, Output $output, array $middlewareResult) : bool {

		$result = [
			'lastUpdate' => date('Y-m-d H:i:s'),
			'filesCount' => 0,
			'logsCount' => 0,
			'logs' => []
		];

		# Get Log files
		$files = glob($this->logFilesPathPattern);

		if(empty($files)) {
			file_put_contents($this->logFilesPath.'stats.json', json_encode($result, JSON_PRETTY_PRINT));
			return false;
		}

		# Open files and collect logs
		foreach ($files as $file) {

			$fileContent = file($file);

			if(empty($fileContent)) {
				continue;
			}

			foreach ($fileContent as $line) {

				# Parse Log file line
				$logData = Logger::parseLogLine($line);

				if(!isset($result['logs'][$logData['type']])) {
					$result['logs'][$logData['type']] = 0;
				}

				$result['logs'][$logData['type']]++;

				$result['logsCount']++;

			}

			$result['filesCount']++;

		}

		file_put_contents($this->logFilesPath.'stats.json', json_encode($result, JSON_PRETTY_PRINT));

		return true;

	}

    /**
	 * HTTP Request to get Application logs statistics
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param array $middlewareResult
	 * @return bool
	 */
	public function get(Request $request, Response $response, array $middlewareResult) : bool {

		# Logs statistics file
		$file = DUKTIG_APP_PATH . 'log/stats.json';

		# Let's check if the file exists
		if(!is_file($file)) {

			$response->status(204);
			$response->sendFinal();
			
			return False;
		}

		$response->sendJsonString(file_get_contents($file));

		return true;

	}

}