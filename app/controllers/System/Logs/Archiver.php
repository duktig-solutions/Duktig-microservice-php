<?php
/**
 * Application logs processor - Archiver
 *
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */

namespace App\Controllers\System\Logs;

use System\CLI\Input;
use System\CLI\Output;

/**
 * Class Archiver
 *
 * @package App\Controllers\General
 */
class Archiver {

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
	 * @param \System\CLI\Input $input
	 * @param \System\CLI\Output $output
	 * @param array $middlewareResult
	 * @return bool
	 */
	public function process(Input $input, Output $output, array $middlewareResult) : bool {

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

}