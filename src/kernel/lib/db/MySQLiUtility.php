<?php
/**
 * MySQLi Utility class
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace Lib\Db;

use Exception;

/**
 * Class MySQLiUtility
 *
 * @package Lib\Db
 */
class MySQLiUtility extends \Lib\Db\MySQLi {

	/**
	 * Return list of tables
	 *
	 * @access public
	 * @throws Exception
	 * @return array
	 */
	public function getTables() : array {

		$result = $this->query("show tables");

		if(!$result->num_rows) {
			return [];
		}

		$tables = [];

		while($row = $result->fetch_assoc()) {
			$tables[] = $row[key($row)];
		}

		return $tables;
	}

	/**
	 * Get Create table syntax
	 *
	 * @access public
	 * @param string $tableName
	 * @throws Exception
	 * @return string
	 */
	public function getCreateTable(string $tableName) : string {

		$result = $this->fetchAssoc("show create table `" . $this->escape($tableName) . "`");

		return $result['Create Table'];

	}

}