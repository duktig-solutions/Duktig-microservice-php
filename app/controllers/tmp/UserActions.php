<?php
/**
 * User Actions Controller
 * This controller only available to access for Admin and Super Admin. Checking permissions in Auth middleware.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers;

use System\Request;
use System\Response;
use System\Input;
use System\Output;
use App\Models\UserActions as UserActionsModel;
use \App\Models\Statistics;

/**
 * Class UserActions
 *
 * @package App\Controllers
 */
class UserActions {

	/**
	 * Statistics ID for statistics table data access
	 *
	 * @const
	 */
	const STAT_ID = 'user_actions';

	/**
	 * Generate user Actions statistics
	 *
	 * @param Input $input
	 * @param Output $output
	 * @param array $middlewareResult
	 * @throws \Exception
	 * @return bool
	 */
	public function generateUserActionsStats(Input $input, Output $output, array $middlewareResult) : bool {

		$statsModel = new Statistics();

		$result = [
			'totalStats' => $this->generateUserActionsStatsParts(),
			'systemStats' => $this->generateUserActionsStatsParts(USER_ID_SYSTEM),
			'usersStats' => $this->generateUserActionsStatsParts(USER_ID_USER),
			'guestsStats' => $this->generateUserActionsStatsParts(USER_ID_GUEST),
			'lastStatUpdateDate' => date('Y-m-d H:i:s')
		];

		$statsModel->autoUpdate(self::STAT_ID, json_encode($result, JSON_PRETTY_PRINT));

		return true;

	}

	/**
	 * Generate part of User actions stats
	 *
	 * @access private
	 * @param int|null $userType
	 * @return array
	 * @throws \Exception
	 */
	private function generateUserActionsStatsParts(?int $userType = null) : array {

		$userActionsModel = new UserActionsModel();

		$result = [];

		$actionsData = $userActionsModel->getStatsGeneral($userType);

		foreach ($actionsData as $label => $stats) {

			if(!isset($result[$label])) {
				$result[$label] = [
					'byTypeCount' => [],
					'byTypePercentage' => []
				];
			}

			$totalStats = 0;

			foreach ($stats as $stat) {

				# Count
				$result[$label]['byTypeCount'][$stat['actionCode']] = $stat['cnt'];

				$totalStats += $stat['cnt'];

			}

			foreach ($stats as $stat) {
				# Percentage
				$result[$label]['byTypePercentage'][$stat['actionCode']] = round($stat['cnt'] / ($totalStats / 100), 2);
			}
		}

		return $result;

	}

	/**
	 * Get user account by id
	 *
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function getUserActionsStats(Request $request, Response $response, array $middlewareData) : bool {

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
