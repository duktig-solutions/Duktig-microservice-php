<?php
/**
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models;

/**
 * Class UserActions
 *
 * @package App\Models
 */
class UserActions extends \Lib\Db\MySQLi
{

	/**
	 * Each model can contain it's own database table name(s).
	 *
	 * @access private
	 * @var string
	 */
	private $table = 'userActions';

	public function __construct() {

		$config = \System\Config::get()['Databases']['DefaultConnection'];

		parent::__construct($config);
	}

	/**
	 * Insert user action
	 *
	 * @param int $userId
	 * @param string $actionMessage
	 * @param string $actionCode
	 * @return int
	 */
	public function insertAction(int $userId, string $actionMessage, string $actionCode) : int {
		return $this->insertRow([
			'userId' => $userId,
			'actionMessage' => $actionMessage,
			'actionCode' => $actionCode
		]);
	}

	/**
	 * Insert row
	 *
	 * @access public
	 * @param array $data
	 * @return int
	 */
	public function insertRow(array $data) : int {

		$default = [
			# Assuming this is guest
			'userId' => 0,
			'dateAction' => date('Y-m-d H:i:s'),
			'actionMessage' => '',
			'actionCode' => 'Unknown action'
		];

		# Merge Default values with request values
		$record = array_merge($default, $data);

		# Insert and return id
		return $this->insert($this->table, $record);

	}

	/**
	 * Return Statistics
	 *
	 * @access public
	 * @param int|null $userType
	 * @throws \Exception
	 * @return array
	 */
	public function getStatsGeneral(?int $userType) : array {

		return [
			'Total' => $this->getStatsCustom(
				null,
				$userType
			),
			'Last year' => $this->getStatsCustom(
				'DATE(dateAction) > DATE(NOW() - INTERVAL 1 YEAR)',
				$userType
			),
			'Last 3 months' => $this->getStatsCustom(
				'DATE(dateAction) >= DATE(NOW() - INTERVAL 3 MONTH)',
				$userType
			),
			'Last 30 days' => $this->getStatsCustom(
				'DATE(dateAction) >= DATE(NOW() - INTERVAL 30 DAY)',
				$userType
			),
			'Last 24 hours' => $this->getStatsCustom(
				'DATE(dateAction) >= DATE(NOW() - INTERVAL 24 HOUR)',
				$userType
			),
			'Last hour' => $this->getStatsCustom(
				'DATE(dateAction) >= DATE(NOW() - INTERVAL 1 HOUR)',
				$userType
			)
		];

	}

	/**
	 * Run custom query to get Stats and return result
	 *
	 * @access public
	 * @param string|null $timeInterval
	 * @param int|null $userType
	 * @return array
	 * @throws \Exception
	 */
	public function getStatsCustom(?string $timeInterval, ?int $userType) : array {

		$queryString = "
			select 
				distinct actionCode, 
			    count(actionCode) as cnt 
			from userActions
			where actionCode != '' 
		";

		if(!is_null($timeInterval)) {
			$queryString .= " and " . $timeInterval . " ";
		}

		if($userType == USER_ID_GUEST) {
			$queryString .= " and  userId = " . USER_ID_GUEST . " ";
		} elseif($userType == USER_ID_SYSTEM) {
			$queryString .= " and  userId = " . USER_ID_SYSTEM . " ";
		} elseif($userType == USER_ID_USER) {
			$queryString .= " and  userId > 0 ";
		}

		$queryString .= " group by actionCode;";

		return $this->fetchAllAssoc($queryString);

	}

	/**
	 * Get User actions by criteria
	 *
	 * @access public
	 * @param int $userId
	 * @param int $dateFrom
	 * @param int $dateTo
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @throws \Exception
	 */
	public function fetchByCriteria(int $userId, int $dateFrom, int $dateTo, int $offset, int $limit) : array {

		return $this->fetchAllAssoc("
			select 
			    dateAction,
			    actionCode,
			    actionMessage
			from userActions
			where userId = ?
			and dateAction >= DATE_FORMAT(FROM_UNIXTIME(?), '%Y-%m-%d %H:%i:%s')
			and dateAction >= DATE_FORMAT(FROM_UNIXTIME(?), '%Y-%m-%d %H:%i:%s')
			order by dateAction
			limit ?, ?  ",
			[
				$userId,
				date('Y-m-d H:i:s', $dateFrom),
				date('Y-m-d H:i:s', $dateTo),
				$offset,
				$limit
			]
		);

	}

}