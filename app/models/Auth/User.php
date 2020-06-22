<?php
/**
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Auth;

/**
 * Class User
 *
 * @package App\Models
 */
class User extends \Lib\Db\MySQLi
{

    /**
     * Each model can contain it's own database table name(s).
     *
     * @access private
     * @var string
     */
    private $table = 'users';

    public function __construct() {

        $config = \System\Config::get()['Databases']['Auth'];

        parent::__construct($config);
    }

    /**
     * Fetch User Account by email [and Id]
     *
     * @access public
     * @param string $email
     * @param int|null $userId
     * @throws \Exception
     * @return bool
     */
    public function emailExistsById(string $email, ?int $userId = NULL) : bool {

    	$query = "select * from users where email = ? ";
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

    public function fetchRow(array $where) : array {
        return $this->fetchAssocByWhere($this->table, $where);
    }

    public function fetchPublicRow(array $where) : array {

        $result = $this->fetchAssocByWhere($this->table, $where);

        if(!$result) {
            return [];
        }

        unset($result['password']);
        unset($result['comment']);
        unset($result['status']);
        unset($result['roleId']);

        return $result;
    }

    public function insertRow($data) {

        $default = [
            'dateRegistered' => date('Y-m-d H:i:s'),
            'roleId' => 1
        ];

        // Let's encrypt the password
        $data['password'] = \Lib\Auth\Password::encrypt($data['password']);

        // Merge Default values with request values
        $record = array_merge($default, $data);

        // Insert and return id
        return $this->insert($this->table, $record);

    }

    public function updateRow($data, $where) {

        $default = [
            'dateLastUpdate' => date('Y-m-d H:i:s'),
        ];

        // Let's encrypt password if set
        if(isset($data['password'])) {
            $data['password'] = \Lib\Auth\Password::encrypt($data['password']);
        }

        $record = array_merge($default, $data);

        return $this->update($this->table, $record, $where);

    }

}