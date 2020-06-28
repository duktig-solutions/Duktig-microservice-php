<?php
/**
 * Application logs processor
 *
 * - Archive logs
 * - Create Logs statistics
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */

namespace App\Controllers\system;

use System\Logger;
use System\Input;
use System\Output;

/**
 * Class AppLogsProcessor
 *
 * @package App\Controllers\General
 */
class AppLogsProcessor {

	/**
	 * File size to move to archive
	 * Default 500kb
	 *
	 * @access private
	 * @var int
	 */
	private $logFileSizeToArchive = 500000;

	/**
	 * Pattern to get log files (path with file type)
	 *
	 * @access private
	 * @var string
	 */
	private $logFilesPathPattern;

	/**
	 * Log Files path
	 *
	 * @access private
	 * @var string
	 */
	private $logFilesPath;

	/**
	 * AppLogs constructor.
	 */
	public function __construct() {

		# Initialize Log files path/pattern
		$this->logFilesPathPattern = DUKTIG_APP_PATH . 'log/*.log';
		$this->logFilesPath = DUKTIG_APP_PATH . 'log/';
	}

	/**
	 * This method archives large log files
	 *
	 * @access public
	 * @param \System\Input $input
	 * @param \System\Output $output
	 * @param array $middlewareResult
	 * @return bool
	 */
	public function archiveLogs(\System\Input $input, \System\Output $output, array $middlewareResult) : bool {

		$files = glob($this->logFilesPathPattern);

		# There are no log files.
		# Nothing to do
		if(empty($files)) {
			$output->stdout("No files to archive.");
			return false;
		}

		# Loop for each file and check the content size
		foreach ($files as $file) {

			# Check if size is relevant for archive and the file is not already archived.
			if(filesize($file) >= $this->logFileSizeToArchive and substr($file, -13) != '_archived.log') {

				$pathParts = pathinfo($file);
				$newName = $pathParts['filename'].'_'.date('Y-m-d_H.i.s').'_archived.log';

				$output->stdout("Archive: " . basename($file));

				rename(
					$file,
					DUKTIG_APP_PATH . 'log/' . $newName
				);

			}


		}

		return true;

	}

	// Collect logs stats
	public function makeStats(Input $input, Output $output, array $middlewareResult) : bool {

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

}