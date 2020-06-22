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
namespace App\Controllers\General;
use \System\Logger;

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
	 * AppLogs constructor.
	 */
	public function __construct() {

		# Initialize Log files path/pattern
		$this->logFilesPathPattern = DUKTIG_APP_PATH . 'log/*.log';

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

	// Collect logs info and insert into self stats table
	// @todo finalize this
	public function collectLogsToSelfStats(Input $input, Output $output, array $middlewareResult) : bool {

		$systemId = 'this is system id of instance. i.e. dataCollector, dataReception etc...';

		$result = [
			'subject' => 'Application Logs',
			'lastUpdate' => date('Y-m-d H:i:s'),
			'appendix' => [
				'logFilesCount' => 0,
				'totalLogLines' => 0,
				'firstLog' => [],
				'lastLog' => [],
				'firstLogDate' => [],
				'lastLogDate' => [],
			],
			'data' => [
				'last_12_months' => [
					'type' => 'line',
					'title' => 'Last year',
					'xAxis' => [],
					'yAxis' => [
						'min' => 0,
						'max' => 0
					],
					'series' => []
				],
				'last_30_days' => [
					'type' => 'line',
					'title' => 'Last 30 days',
					'xAxis' => [],
					'yAxis' => [
						'min' => 0,
						'max' => 0
					],
					'series' => []
				],
				'last_24_hours' => [
					'type' => 'line',
					'title' => 'Last 24 hours',
					'xAxis' => [],
					'yAxis' => [
						'min' => 0,
						'max' => 0
					],
					'series' => []
				],
				'last_1_hour' => [
					'type' => 'line',
					'title' => 'Last 1 hour',
					'xAxis' => [],
					'yAxis' => [
						'min' => 0,
						'max' => 0
					],
					'series' => []
				]
			]
		];

		# Last 12 months
		$xAxisTemplate = [];
		$dataTemplate = [];

		# Last 12 months
		for($i = 0; $i <= 11; $i++) {
			$xAxisTemplate[] = date('M', strtotime('-'.$i.' month'));
			$dataTemplate[date('Y-m', strtotime('-'.$i.' month'))] = 0;
		}

		$xAxisTemplate = array_reverse($xAxisTemplate);
		$dataTemplate = array_reverse($dataTemplate);

		$result['data']['last_12_months']['xAxis'] = $xAxisTemplate;

		foreach (\System\Logger::types() as $type) {

			$result['appendix']['firstLog'][$type] = '';
			$result['appendix']['lastLog'][$type] = '';

			$result['appendix']['firstLogDate'][$type] = '';
			$result['appendix']['lastLogDate'][$type] = '';

			$result['data']['last_12_months']['series'][$type] = [
				'title' => $type,
				'data' => $dataTemplate
			];

		}

		# Last 30 days
		$xAxisTemplate = [];
		$dataTemplate = [];

		for($i = 0; $i <= 30; $i++) {

			$xAxisTemplate[] = date('M d', strtotime('-'.$i.' day'));
			$dataTemplate[date('Y-m-d', strtotime('-'.$i.' day'))] = 0;

		}

		$xAxisTemplate = array_reverse($xAxisTemplate);
		$dataTemplate = array_reverse($dataTemplate);

		$result['data']['last_30_days']['xAxis'] = $xAxisTemplate;

		foreach (\System\Logger::types() as $type) {
			$result['data']['last_30_days']['series'][$type] = [
				'title' => $type,
				'data' => $dataTemplate
			];
		}

		# Last 24 hours
		$xAxisTemplate = [];
		$dataTemplate = [];

		for($i = 0; $i < 24; $i++) {

			$xAxisTemplate[] = date('H:00', strtotime('-'.$i.' hour'));
			$dataTemplate[date('Y-m-d H:00', strtotime('-'.$i.' hour'))] = 0;

		}

		$xAxisTemplate = array_reverse($xAxisTemplate);
		$dataTemplate = array_reverse($dataTemplate);

		$result['data']['last_24_hours']['xAxis'] = $xAxisTemplate;

		foreach (\System\Logger::types() as $type) {
			$result['data']['last_24_hours']['series'][$type] = [
				'title' => $type,
				'data' => $dataTemplate
			];
		}

		# Last 1 hour
		$xAxisTemplate = [];
		$dataTemplate = [];

		for($i = 0; $i <= 60; $i++) {

			$xAxisTemplate[] = date('H:i', strtotime('-'.$i.' minute'));
			$dataTemplate[date('Y-m-d H:i', strtotime('-'.$i.' minute'))] = 0;

		}

		//$xAxisTemplate = array_reverse($xAxisTemplate);
		$dataTemplate = array_reverse($dataTemplate);

		foreach (\System\Logger::types() as $type) {
			$result['data']['last_1_hour']['series'][$type] = [
				'title' => $type,
				'data' => $dataTemplate
			];
		}

		$result['data']['last_1_hour']['xAxis'] = $xAxisTemplate;

		# Next steps

		$statsModel = new Statistics();

		$files = glob($this->logFilesPathPattern);

		if(empty($files)) {
			$statsModel->autoUpdate(self::STAT_ID, json_encode($result, JSON_PRETTY_PRINT));
			return false;
		}

		$result['appendix']['logFilesCount'] = count($files);
		$totalLogLines = 0;

		foreach ($files as $file) {

			$fileContent = file($file);

			if(empty($fileContent)) {
				continue;
			}

			foreach ($fileContent as $line) {

				# Parse Log file line
				$logData = \System\Logger::parseLogLine($line);

				# Last 12 months
				$dateKey = date('Y-m', strtotime($logData['date']));

				if(isset($result['data']['last_12_months']['series'][$logData['type']]['data'][$dateKey])) {
					$result['data']['last_12_months']['series'][$logData['type']]['data'][$dateKey]++;

					# Set max number - yAxis
					if($result['data']['last_12_months']['series'][$logData['type']]['data'][$dateKey] > $result['data']['last_12_months']['yAxis']['max']) {
						$result['data']['last_12_months']['yAxis']['max'] = $result['data']['last_12_months']['series'][$logData['type']]['data'][$dateKey];
					}
				}

				# Last 30 days
				$dateKey = date('Y-m-d', strtotime($logData['date']));

				if(isset($result['data']['last_30_days']['series'][$logData['type']]['data'][$dateKey])) {
					$result['data']['last_30_days']['series'][$logData['type']]['data'][$dateKey]++;

					# Set max number - yAxis
					if($result['data']['last_30_days']['series'][$logData['type']]['data'][$dateKey] > $result['data']['last_30_days']['yAxis']['max']) {
						$result['data']['last_30_days']['yAxis']['max'] = $result['data']['last_30_days']['series'][$logData['type']]['data'][$dateKey];
					}
				}

				# Last 24 hours
				$dateKey = date('Y-m-d H:00', strtotime($logData['date']));

				if(isset($result['data']['last_24_hours']['series'][$logData['type']]['data'][$dateKey])) {
					$result['data']['last_24_hours']['series'][$logData['type']]['data'][$dateKey]++;

					# Set max number - yAxis
					if($result['data']['last_24_hours']['series'][$logData['type']]['data'][$dateKey] > $result['data']['last_24_hours']['yAxis']['max']) {
						$result['data']['last_24_hours']['yAxis']['max'] = $result['data']['last_24_hours']['series'][$logData['type']]['data'][$dateKey];
					}

				}

				# Last 1 hour
				$dateKey = date('Y-m-d H:i', strtotime($logData['date']));

				if(isset($result['data']['last_1_hour']['series'][$logData['type']]['data'][$dateKey])) {
					$result['data']['last_1_hour']['series'][$logData['type']]['data'][$dateKey]++;

					# Set max number - yAxis
					if($result['data']['last_1_hour']['series'][$logData['type']]['data'][$dateKey] > $result['data']['last_1_hour']['yAxis']['max']) {
						$result['data']['last_1_hour']['yAxis']['max'] = $result['data']['last_1_hour']['series'][$logData['type']]['data'][$dateKey];
					}

				}

				# Appendix
				if($result['appendix']['firstLogDate'][$logData['type']] == '') {
					$result['appendix']['firstLog'][$logData['type']] = $logData['message'];
					$result['appendix']['firstLogDate'][$logData['type']] = $logData['date'];
				} elseif(strtotime($logData['date']) < strtotime($result['appendix']['firstLogDate'][$logData['type']])) {
					$result['appendix']['firstLog'][$logData['type']] = $logData['message'];
					$result['appendix']['firstLogDate'][$logData['type']] = $logData['date'];
				}

				if(strtotime($logData['date']) > strtotime($result['appendix']['lastLogDate'][$logData['type']])) {
					$result['appendix']['lastLog'][$logData['type']] = $logData['message'];
					$result['appendix']['lastLogDate'][$logData['type']] = $logData['date'];
				}

				$totalLogLines++;

			}

		}

		print_r($result);
		$result['appendix']['totalLogLines'] = $totalLogLines;

		$statsModel->autoUpdate(self::STAT_ID, json_encode($result, JSON_PRETTY_PRINT));

		return true;

	}

}