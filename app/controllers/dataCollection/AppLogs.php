<?php
/**
 * Application Logs Statistic
 *
 * - Get all logs of all instances
 * - Get logs by instance
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
*/
namespace App\Controllers\dataCollection;

use \System\Request;
use \System\Response;
use \App\Models\Statistics;

/**
 * Class AppLogs
 *
 * @package App\Controllers
 */
class AppLogs {

	/**
	 * Statistics ID for statistics table data access
	 *
	 * @const
	 */
	const STAT_ID = 'app_logs';

	/**
	 * Response Log Files statistics
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @throws \Exception
	 * @return bool
	 */
	public function appLogs(Request $request, Response $response, array $middlewareData) : bool {

		$statsModel = new Statistics();

		$statsData = $statsModel->fetchById(self::STAT_ID);

		if(empty($statsData)) {
			$response->status(204);
			return false;
		}

		$response->sendJsonString(
			$statsData['statisticsJson'],
			200
		);

		return true;

	}

}