<?php
/**
 * DataReception model
 */
namespace App\Models\DataReception;

/**
 * Class DataReception
 *
 * @package App\Models\DataReception
 */
class DataReception extends \Lib\Db\MySQLi  {

	private $table = 'dataReceived';

	public function insertDataReceived($data) {
		return $this->insert($this->table, $data);
	}

}