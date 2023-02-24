<?php
/**
 * PostgreSQL Class library
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.2
 */
namespace Lib\Db;

use Throwable;

/**
 * Class PostgreSQL
 *
 * @package Lib\Db
 */
class PostgreSQL {

    /**
     * Configuration values
     *
     * @access protected
     * @var array
     */
    protected $config = NULL;

    /**
     * Connection resource
     *
     * @access protected
     * @var resource
     */
    protected $conn = null;

    /**
     * PostgreSQL Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
        $this->conn = $this->connect($config);
    }

    /**
     * Class destructor
     *
     * @access public
     */
    public function __destruct() {

        # When developing this code: listening - Hungarian Dance Radio and planning to Visit Stuttgart Germany (Feb 2022)
        $this->close();

    }

    /**
     * Connect to PostgreSQL server
     *
     * @final
     * @access protected
     * @param array $config
     * @return resource
     */
    final protected function connect($config) {

        # Set port if defined
        if ($config['port'] != '') {
            $port = "port=".$config['port']." ";
        } else {
            $port = "";
        }

        if($config['client_encoding'] != '') {
            $client_encoding = "options='--client_encoding=".$config['client_encoding']."'";
        } else {
            $client_encoding = '';
        }

        try {
            $conn = pg_connect("host=".$config['host']." ".$port."dbname=".$config['database']." user=".$config['username']." password=".$config['password']." ".$client_encoding, PGSQL_CONNECT_FORCE_NEW);
        } catch(Throwable $e) {
            \System\Logger::Log($e->getMessage(), \System\Logger::CRITICAL, __FILE__, __LINE__);
            return false;
        }

        return $conn;
    }

    /**
     * Disconnect from PostgreSQL server
     *
     * @final
     * @access protected
     * @return bool
     */
    final protected function close() : bool {
        
        if($this->conn) {
            try {
                pg_close($this->conn);
                $this->conn = null;
            } catch(\Throwable $e) {
                return false;
            }
        }
        
        return false;
    }

    /**
     * Return true if connected
     * 
     * @access public
     * @return bool
     */
    public function connected() : bool {
        
        if(!$this->conn) {
            return false;
        }

        return true;
    }

    /**
     * Return last insert id
     *
     * @final
     * @access protected
     * @param mixed $query_result
     * @return int
     */
    final protected function insertId($query_result) : int {
        return pg_last_oid($query_result);
    }

    /**
     * Insert method
     *
     * @final
     * @access public
     * @param string $table
     * @param array $data
     * @param mixed string|null $returnInsertIdFieldName
     * @return mixed int|boolean
     */
    final public function insert(string $table, array $data, ?string $returnInsertIdFieldName = null) {
        
        $sql = "INSERT INTO ".$this->escape($table)." (";
        $valuesStr = "";

        $i = 1;
        foreach($data as $field => $value) {
            $sql .= $field.", ";
            $valuesStr .= "$".$i.", ";
            $i++;
        }
        
        $sql = rtrim($sql, ", ");
        $valuesStr = rtrim($valuesStr, ", ");

        $sql .= ") VALUES (".$valuesStr.") ";

        if(!is_null($returnInsertIdFieldName)) {
            $sql .= " RETURNING " . $this->escape($returnInsertIdFieldName);
        } 
        
        $result = $this->query($sql, array_values($data));

        if(!$result) {
            return false;
        }

        if(!is_null($returnInsertIdFieldName)) {
            return pg_fetch_result($result, 0, $returnInsertIdFieldName);
        } else {
            return true;
        }

    }

    /**
     * Insert Batch records
     *
     * @final
     * @access public
     * @param string $table
     * @param array $fields
     * @param array $data
     * @param mixed string|null $returnInsertIdFieldName
     * @return mixed bool|array
     */
    final public function insertBatch(string $table, array $fields, array $data, ?string $returnInsertIdFieldName = null) {

        $sql = "INSERT INTO ".$this->escape($table)." (";
        $valuesStr = "";

        foreach($fields as $field) {
            $sql .= $field.", ";
        }
        
        $sql = rtrim($sql, ", ");
        $sql .= ") VALUES ";

        $i = 1;
        $insertData = [];
        foreach($data as $record) {
            $sql .= "(";
            foreach($record as $value) {                
                $sql .= "$".$i.", ";
                $i++;

                $insertData[] = $value;
            }
            $sql = rtrim($sql, ", ");
            $sql .= "), ";
        }

        $sql = rtrim($sql, ", ");

        if(!is_null($returnInsertIdFieldName)) {
            $sql .= " RETURNING ".$returnInsertIdFieldName;
        }

        $result = $this->query($sql, $insertData);

        if(!$result) {
            return false;
        }

        if(!is_null($returnInsertIdFieldName)) {
            return pg_fetch_all_columns($result);
        } else {
            return true;
        }

    }

    /**
     * Update records
     *
     * @final
     * @access public
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int
     */
    final public function update(string $table, array $data, array $where) : int {

        $sql = "UPDATE ".$this->escape($table)." SET ";
        $queryValues = [];   
        
        $i = 1;
        foreach($data as $field => $value) {
            $sql .= $field."=$".$i.", ";
            $queryValues[] = $value;
            $i++;
        }

        $sql = rtrim($sql, ", ");
        $sql .= " WHERE ";

        foreach($where as $field => $value) {
            $sql .= $field."=$".$i." and ";
            $queryValues[] = $value;
            $i++;
        }

        $sql = rtrim($sql, " and ");

        $result = $this->query($sql, $queryValues);

        return pg_affected_rows($result);
    }

    /**
     * Delete records
     *
     * @final
     * @access public
     * @param string $table
     * @param array $where
     * @return int
     */
    final public function delete(string $table, array $where) : int {

        $sql = "DELETE FROM ".$this->escape($table)." WHERE ";
        $queryValues = [];   
        
        $i = 1;
        foreach($where as $field => $value) {
            $sql .= $field."=$".$i." and ";
            $queryValues[] = $value;
            $i++;
        }

        $sql = rtrim($sql, " and ");

        $result = $this->query($sql, $queryValues);

        return pg_affected_rows($result);

    }

    /**
     * Regular PostgreSQL Query
     *
     * @final
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @return mixed PgSql\Result|false
     * @throws \Exception
     */
    final public function query(string $queryString, ?array $params = NULL) {

        try {
            if(is_null($params)) {
                $result = pg_query($this->conn, $queryString);
            } else {
                $result = pg_query_params($this->conn, $queryString, $params);
            }
        } catch(\Throwable $e) {
            throw new \Exception(\pg_last_error($this->conn));
        }
        
        if (!$result) {
            throw new \Exception(\pg_last_error($this->conn));
        }

        return $result;

    }

    /**
     * Run query and return affected rows
     *
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @return mixed false|int
     */
    public function queryWithAffectedRows(string $queryString, ?array $params = NULL) {

        $result = $this->query($queryString, $params);

        if(!$result) {
            return false;
        }

        return pg_affected_rows($result);
    }

    /**
     * Fetch records and return Assoc array
     *
     * @final
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @throws \Exception
     * @return mixed bool|array
     */
    final public function fetchAllAssoc(string $queryString, ?array $params = NULL) {

        $result = $this->query($queryString, $params);

        if (!$result) {
            return false;
        }

        return pg_fetch_all($result);

    }

    /**
     * Fetch record and return Assoc array
     *
     * @final
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @throws \Exception
     * @return mixed Bool|Array
     */
    final public function fetchAssoc(string $queryString, ?array $params = NULL) {

        $result = $this->query($queryString, $params);

        if (!$result) {
            return [];
        }
    
        return pg_fetch_assoc($result);

    }

    /**
     * Fetch Records as assoc array by Where conditions
     *
     * @final
     * @access public
     * @param string $table
     * @param array $where
     * @return mixed bool|array
     */
    final public function fetchAllAssocByWhere(string $table, array $where) {

        $sql = "SELECT * FROM ".$this->escape($table)." ";
        $sql .= ' WHERE ';
        
        $i = 1;
        
        foreach($where as $field => $value) {
            $sql .= $field . " = $".$i." and ";
            $i++;
        }

        $sql = rtrim($sql, " and ");
        
        return $this->fetchAllAssoc($sql, array_values($where));

    }

    /**
     * Fetch Records with specified fields as assoc array by Where conditions
     *
     * @final
     * @access public
     * @param string $table
     * @param array $fields
     * @param array $where
     * @return mixed bool|array
     */
    final public function fetchAllFieldsAssocByWhere(string $table, array $fields, array $where) {

        $sql = "SELECT ".implode(',', $fields)." FROM ".$this->escape($table)." ";
        $sql .= ' WHERE ';
        
        $i = 1;
        
        foreach($where as $field => $value) {
            $sql .= $field . " = $".$i." and ";
            $i++;
        }

        $sql = rtrim($sql, " and ");
        
        return $this->fetchAllAssoc($sql, array_values($where));
        
    }

    /**
	 * Fetch Record as assoc array by Where conditions
	 *
	 * @final
	 * @access public
	 * @param string $table
	 * @param array $where
	 * @return mixed bool|array
	 */
	final public function fetchAssocByWhere(string $table, array $where) {

        $sql = "SELECT * FROM ".$this->escape($table)." ";
        $sql .= ' WHERE ';
        
        $i = 1;
        
        foreach($where as $field => $value) {
            $sql .= $field . " = $".$i." and ";
            $i++;
        }

        $sql = rtrim($sql, " and ");

        return $this->fetchAssoc($sql, array_values($where));

	}

    /**
	 * Fetch Record specified fields as assoc array by Where conditions
	 *
	 * @final
	 * @access public
	 * @param string $table
     * @param array $fields
	 * @param array $where
	 * @return mixed bool|array
	 */
	final public function fetchFieldsAssocByWhere(string $table, array $fields, array $where) {

        $sql = "SELECT ".implode(',', $fields)." FROM ".$this->escape($table)." ";
        $sql .= ' WHERE ';
        
        $i = 1;
        
        foreach($where as $field => $value) {
            $sql .= $field . " = $".$i." and ";
            $i++;
        }

        $sql = rtrim($sql, " and ");
        
        return $this->fetchAssoc($sql, array_values($where));

	}

    /**
     * Escape string
     *
     * @access public
     * @param mixed $value
     * @return string
     */
    final public function escape($value) : string {
        return pg_escape_string($this->conn, $value);
    }

    /**
     * Begin transaction
     *
     * @access public
     * @return void
     */
    final public function beginTrans() {
        pg_query($this->conn, "BEGIN");
    }

    /**
     * Commit transaction
     *
     * @access public
     * @return void
     */
    final public function commitTrans() {
        pg_query($this->conn, "COMMIT");
    }

    /**
     * Rollback transaction
     *
     * @access public
     * @return void
     */
    final public function rollbackTrans() {
        pg_query($this->conn, "ROLLBACK");
    }


}

