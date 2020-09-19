<?php
/**
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Users;

/**
 * Class Users
 *
 * @package App\Models
 */
class Users extends \Lib\Db\MySQLi
{

	/**
	 * Each model can contain it's own database table name(s).
	 *
	 * @access private
	 * @var string
	 */
	private $table = 'users';

	public function __construct() {

		$config = \System\Config::get()['Databases']['Users'];

		parent::__construct($config);
	}

	/**
	 * Fetch row but with more details for admin
	 *
	 * @param array $where
	 * @return array
	 */
	public function fetchRow(array $where) : array {

		$result = $this->fetchAssocByWhere($this->table, $where);

		if(!empty($result)) {
			unset($result['password']);
		}

		return $result;

	}

	/**
	 * Fetch Users
	 *
	 * @param $offset
	 * @param $limit
	 * @return array
	 * @throws \Exception
	 */
	public function fetchRows($offset, $limit) {

		return $this->fetchAllAssoc(
			"select 
			userId, firstName, lastName, email, phone, comment, dateRegistered, dateLastUpdate, dateLastLogin, roleId, status
	    	from ".$this->table." limit ?, ?",
			[$offset, $limit]
		);
	}

}