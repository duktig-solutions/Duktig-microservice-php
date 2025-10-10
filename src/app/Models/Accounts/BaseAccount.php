<?php
/**
 * Base Accounts model for microservice
 * 
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.1
 *
 */
namespace App\Models\Accounts;

use System\Config;

/**
 * Class BaseAccount
 *
 * @package App\Models
 */
class BaseAccount extends \Lib\Db\MySQLi
{

    /**
     * Each model can contain its own database table name(s).
     *
     * @access protected
     * @var string
     */
    protected string $table = 'Users';

    /**
     * User accessible fields to return
     *
     * @access protected
     * @var array
     */
    protected array $whitelist = [
        'userId',
        'displayName',
        'firstName',
        'lastName'
    ];

    public function __construct() {
        $config = Config::get()['Databases']['Accounts'];
        parent::__construct($config);
    }

    /**
     * Return true/false if user exists by email [and Id]
     *
     * @access public
     * @param string $email
     * @param int|null $userId
     * @throws \Exception
     * @return bool
     */
    public function emailExistsById(string $email, ?int $userId = NULL) : bool {

    	$query = "select email from ".$this->table." where email = ? ";
        $params = [$email];

        if(!is_null($userId)) {
            $query .= " and userId <> ? ";
            $params[] = $userId;
        }

        if(!empty($this->fetchAssoc($query, $params))) {
            return true;
        }

        return false;
    }

    /**
     * In case if we need to fetch specific rows.
     *
     * @param array $fields
     * @param array $where
     * @return array
     */
    public function fetchFieldsByWhere(array $fields, array $where) : array {
        return $this->fetchFieldsAssocByWhere($this->table, $fields, $where);
    }

    /**
	 * Fetch account
     *
	 * @param array $where
	 * @return array
	 */
	public function fetchRow(array $where) : array {
		return $this->fetchFieldsAssocByWhere($this->table, $this->whitelist, $where);
	}

    /**
     * Insert user account
     *
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function insertRow(array $data) : int {

        # Default values for account
        $default = [
            'dateRegistered' => date('Y-m-d H:i:s'),
            'roleId' => 1
        ];

        # Let's encrypt the password if specified
	    if(!empty($data['password'])) {
		    $data['password'] = \Lib\Auth\Password::encrypt($data['password']);
	    }

        # Merge Default values with request values
        $record = array_merge($default, $data);

        # Insert and return id
        return $this->insert($this->table, $record);

    }

    public function updateRow($data, $where): int
    {

        $default = [
            'dateUpdated' => date('Y-m-d H:i:s'),
        ];

        # Let's encrypt password if set
        if(isset($data['password'])) {
            $data['password'] = \Lib\Auth\Password::encrypt($data['password']);
        }

        $record = array_merge($default, $data);

        return $this->update($this->table, $record, $where);

    }

}