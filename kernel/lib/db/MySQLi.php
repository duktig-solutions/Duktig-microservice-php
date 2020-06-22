<?php
/**
 * MySQLi Class library
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace Lib\Db;

use System\Ehandler;

/**
 * Class MySQL
 *
 * @package Lib\Db
 */
class MySQLi {

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
     * @var \mysqli
     */
    protected $mysqli;

    /**
     * MySQLi Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
        $this->mysqli = $this->connect($config);
    }

    /**
     * Class destructor
     *
     * @access public
     */
    public function __destruct() {

        # When developing this code: listening - Jay-Jay Johanson - Kings Cross - Old Dog
        if($this->mysqli) {
            $this->close();
        }

    }

    /**
     * Connect to MySQL server
     *
     * @final
     * @access protected
     * @param array $config
     * @return \mysqli
     */
    final protected function connect($config) : \mysqli {

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        # Set port if defined
        if ($config['port'] != '') {
            $config['host'] .= ':' . $config['port'];
        }

        # Connect
        $mysqli = new \mysqli($config['host'], $config['username'], $config['password'], $config['database']);

        # Set charset if defined
        if($config['charset'] != '') {
            $mysqli->set_charset($config['charset']);
        }

        return $mysqli;
    }

    /**
     * Disconnect from MySQL server
     *
     * @final
     * @access protected
     * @return bool
     */
    final protected function close() : bool {
        return $this->mysqli->close();
    }

    /**
     * Return last insert id
     *
     * @final
     * @access protected
     * @return int
     */
    final protected function insertId() : int {
        return $this->mysqli->insert_id;
    }

    /**
     * Insert method
     *
     * @final
     * @access public
     * @param string $table
     * @param array $data
     * @return int
     */
    final public function insert(string $table, array $data) : int {

        $insertId = NULL;

        $sql = "INSERT INTO ".$this->escape($table)." SET ";
        $sql .= $this->prepareFields(array_keys($data), '=?, ', '=?');
        $stmt = $this->mysqli->prepare($sql);
        $types = $this->prepareBindParamTypes($data);
        $stmt->bind_param($types, ...array_values($data));

        $stmt->execute();
        $insertId = $this->mysqli->insert_id;
        $stmt->close();

        return $insertId;

    }

    /**
     * Insert Batch records
     *
     * @final
     * @access public
     * @param string $table
     * @param array $fields
     * @param array $data
     * @return array
     */
    final public function insertBatch(string $table, array $fields, array $data) : array {

        # Returned MySQL insert Ids
        $insertIds = [];

        try {

            $sql = "INSERT INTO " . $this->escape($table) . " SET ";

            $sql .= $this->prepareFields($fields, '=?, ', '=?');
            $stmt = $this->mysqli->prepare($sql);
            $types = $this->prepareBindParamTypes($data[0]);

            $this->beginTrans();

            //$this->mysqli->query("SET autocommit=0");
            //$this->mysqli->query("START TRANSACTION");

            foreach ($data as $row) {

                $stmt->bind_param($types, ...$row);
                $stmt->execute();

                $insertIds[] = $this->mysqli->insert_id;

            }

            $stmt->close();

            $this->commitTrans();
            $this->mysqli->autocommit(true);

            //$this->mysqli->query("COMMIT");
            //$this->mysqli->query("SET autocommit=1");

        } catch(\Throwable $e) {

            $this->rollbackTrans();

            //$this->mysqli->query("ROLLBACK");
            \System\Ehandler::processError($e->getMessage(), 0, $e->getFile(), $e->getLine());

            # Reset insert IDs
            $insertIds = [];

        }

        return $insertIds;

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
        $sql .= $this->prepareFields(array_keys($data), '=?, ', '=?');
        $sql .= ' WHERE ';
        $sql .= $this->prepareFields(array_keys($where), '=? and ', '=?');
        $stmt = $this->mysqli->prepare($sql);
        $types = $this->prepareBindParamTypes(array_merge(array_values($data), array_values($where)));
        $stmt->bind_param($types, ...array_merge(array_values($data), array_values($where)));
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;

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
        $sql .= $this->prepareFields(array_keys($where), '=? and ', '=?');
        $stmt = $this->mysqli->prepare($sql);
        $types = $this->prepareBindParamTypes(array_values($where));
        $stmt->bind_param($types, ...array_values($where));
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;

    }

    /**
     * Regular MySQL Query
     *
     * @final
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @return int|\mysqli_result
     * @throws \Exception
     */
    final public function query(string $queryString, ?array $params = NULL) {

    	try {
		    $stmt = $this->mysqli->prepare($queryString);
	    } catch(\Exception $e) {
    		throw new \Exception('Unable to prepare MySQL statement. Query: ' . str_replace(["\n","\r","\t", "  "], '', $queryString));
	    }

        if(!empty($params)) {
            $types = $this->prepareBindParamTypes(array_values($params));
            $stmt->bind_param($types, ...array_values($params));
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if(!$result) {
	        $result = $stmt->affected_rows;
        }

        $stmt->close();

        return $result;

    }

    /**
     * Execute Asynchronous queries and return a results array.
     * Results will be sorted in array with queries order.
     * i.e. 5th element in result array is the result of 5th query.
     *
     * If a query not have defined "config" array,
     * the library first initialization configuration will be used to connect.
     *
     * Warning! MySQL Query strings should be escaped before.
     *
     * @example Queries example
     *
     * [
     *  [
     *      'config' => array of connection config,
     *      'query' => 'mysql query string'
     *  ],
     *  [
     *      'config' => array of connection config,
     *      'query' => 'mysql query string'
     *  ]
     * ]
     *
     * Another example without config
     *
     * [
     *  [
     *      'query' => 'my sql query string'
     *  ],
     *  [
     *      'query' => 'my sql query string'
     *  ]
     * ]
     *
     * @access public
     * @param array $queries
     * @return array
     */
    final public function asyncQueries(array $queries) : array {

        # make links, queries
        $connLinks = [];
        $results = [];

        # Initialize each query connection
        foreach ($queries as $index => $queryData) {

            if(isset($queryData['config'])) {
                $config = $queryData['config'];
            } else {
                $config = $this->config;
            }

            $connLinks[$index] = $this->connect($config);
            $connLinks[$index]->query($queryData['query'], MYSQLI_ASYNC);

            # This will be used finally to set results
            $results[$connLinks[$index]->thread_id] = NULL;

        }

        $processed = 0;

        do {

            $links = $errors = $reject = [];

            foreach ($connLinks as $link) {
                $links[] = $errors[] = $reject[] = $link;
            }

            if (!mysqli_poll($links, $errors, $reject, 1)) {
                continue;
            }

            foreach ($links as $index => $link) {

                $result = $link->reap_async_query();

                $thread_id = $link->thread_id;

                if(is_object($result)) {
                    $results[$thread_id] = $result->fetch_all(MYSQLI_ASSOC);
                    mysqli_free_result($result);
                } elseif($result !== False) {
                    $results[$thread_id] = $result;
                } else {
                    $results[$thread_id] = False;
                    //throw new \Exception(mysqli_error($link));
                    \System\Ehandler::processError(mysqli_error($link), E_USER_ERROR, __FILE__, __LINE__);
                }

                $processed++;

            }

        } while ($processed < count($connLinks));

        return array_values($results);

    }

    /**
     * Fetch records and return Assoc array
     *
     * @final
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @throws \Exception
     * @return array
     */
    final public function fetchAllAssoc(string $queryString, ?array $params = NULL) : array {

        $result = $this->query($queryString, $params);

        return $result->fetch_all(MYSQLI_ASSOC);

    }

    /**
     * Fetch record and return Assoc array
     *
     * @final
     * @access public
     * @param string $queryString
     * @param array|null $params
     * @throws \Exception
     * @return array
     */
    final public function fetchAssoc(string $queryString, ?array $params = NULL) : array {

        $result = $this->query($queryString, $params);

        $row = $result->fetch_assoc();

        if(!$row) {
            return [];
        }

        return $row;

    }

    /**
     * Fetch Records as assoc array by Where conditions
     *
     * @final
     * @access public
     * @param string $table
     * @param array $where
     * @return array
     */
    final public function fetchAllAssocByWhere(string $table, array $where) : array {

        $sql = "SELECT * FROM ".$this->escape($table)." ";
        $sql .= ' WHERE ';
        $sql .= $this->prepareFields(array_keys($where), '=? and ', '=?');
        $stmt = $this->mysqli->prepare($sql);
        $types = $this->prepareBindParamTypes(array_values($where));
        $stmt->bind_param($types, ...array_values($where));
        $stmt->execute();

        $result = $stmt->get_result();
        $record = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if(!$record) {
            return [];
        }

        return $record;
    }

	/**
	 * Fetch Record as assoc array by Where conditions
	 *
	 * @final
	 * @access public
	 * @param string $table
	 * @param array $where
	 * @return array
	 */
	final public function fetchAssocByWhere(string $table, array $where) : array {

		$sql = "SELECT * FROM ".$this->escape($table)." ";
		$sql .= ' WHERE ';
		$sql .= $this->prepareFields(array_keys($where), '=? and ', '=?');
		$stmt = $this->mysqli->prepare($sql);
		$types = $this->prepareBindParamTypes(array_values($where));
		$stmt->bind_param($types, ...array_values($where));

		$stmt->execute();

		$result = $stmt->get_result();
		$record = $result->fetch_assoc();
		$stmt->close();

		if(!$record) {
			return [];
		}

		return $record;

	}

    /**
     * Escape string
     *
     * @access public
     * @param mixed $value
     * @return string
     */
    final public function escape($value) : string {
        return $this->mysqli->real_escape_string($value);
    }

    /**
     * Begin transaction
     *
     * @access public
     * @param bool $autoCommit = false
     * @return void
     */
    final public function beginTrans(bool $autoCommit = false) {
        $this->mysqli->autocommit($autoCommit);
        $this->mysqli->begin_transaction();
    }

    /**
     * Commit transaction
     *
     * @access public
     * @return void
     */
    final public function commitTrans() {
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);
    }

    /**
     * Rollback transaction
     *
     * @access public
     * @return void
     */
    final public function rollbackTrans() {
        $this->mysqli->rollback();
    }

    /**
     * Prepare fields for binding
     *
     * @final
     * @access protected
     * @param array $data
     * @param string $sep
     * @param string $end
     * @return string
     */
    final protected function prepareFields(array $data, string $sep, string $end) : string {
        return implode($sep, $data) . $end;
    }

    /**
     * Prepare parameters for binding
     *
     * @final
     * @access protected
     * @param array $data
     * @return string
     */
    final protected function prepareBindParamTypes(array $data) : string {

        $types = '';

        # array (name => 'david', age => 39)
        foreach($data as $field => $value) {

            # i	corresponding variable has type int
            # d	corresponding variable has type double
            # s	corresponding variable has type string
            # b	corresponding variable is a blob and will be sent in packets

            if(is_int($value)) {
                $types .= 'i';
            } elseif(is_float($value)) {
                $types .= 'd';
            } elseif(is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }

        return $types;

    }

}