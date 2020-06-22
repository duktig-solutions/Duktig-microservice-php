<?php
/**
 * Statistics Model
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models;

/**
 * Class Statistics
 *
 * @package App\Models
 */
class Statistics extends \Lib\Db\MySQLi
{

	/**
	 * Each model can contain it's own database table name(s).
	 *
	 * @access private
	 * @var string
	 */
	private $table = 'statistics';

	public function __construct() {

		$config = \System\Config::get()['Databases']['DefaultConnection'];

		parent::__construct($config);
	}


	public function fetchById(string $statId) : array {
		return $this->fetchAssocByWhere($this->table, ['statId' => $statId]);
	}

	public function fetchRow(array $where) : array {
		return $this->fetchAssocByWhere($this->table, $where);
	}

	/**
	 * Insert if not exists and Update if exists statistics
	 *
	 * @access public
	 * @param string $statId
	 * @param string $statisticsJson
	 * @return bool
	 * @throws \Exception
	 */
	public function autoUpdate(string $statId, string $statisticsJson) : bool {

		return $this->query("
			INSERT INTO ".$this->table." (statId, dateLastUpdate, statisticsJson)
			VALUES (?, NOW(), ?)
			ON DUPLICATE KEY UPDATE
			statId = ?, 
			dateLastUpdate = NOW(), 
			statisticsJson = ?
			",
			[$statId, $statisticsJson, $statId, $statisticsJson]
		);

	}

}