<?php
/**
 * PostgreSQL Demonstration Model
 * 
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Examples;

use Exception;
use System\Config;
use Throwable;

/**
 * Class PostgreSqlTestModel
 *
 * Model class to demonstrate the PostgreSQL connection and usage with Duktig PHP Framework
 */
class PostgreSqlTestModel extends \Lib\Db\PostgreSQL {

    public function __construct() {
        $config = Config::get()['Databases']['Example_PostgreSQL_SERVER_Connection'];
        parent::__construct($config);
    }

    /**
     * Select all records ass assoc array
     *
     * @access public
     * @throws Exception
     * @retun array|mixed
     */
    public function selectAllAssoc() {
        return $this->fetchAllAssoc("select * from unit_structures where title = $1", ['Server']);
    }

    /**
     * Select assoc
     *
     * @return array
     * @throws Exception
     */
    public function selectAssoc() {
        return $this->fetchAssoc("select * from unit_structures where title = $1", ['Server']);
    }

    /**
     * Select all assoc by where
     *
     * @access public
     * @return array|bool
     * @throws Exception
     */
    public function selectAllAssocByWhere() {
        return $this->fetchAllAssocByWhere('unit_structures', ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    /**
     * Select specified fields of records as assoc arrays by where
     *
     * @access public
     * @return array|bool
     * @throws Exception
     */
    public function selectAllFieldsAssocByWhere() {
        return $this->fetchAllFieldsAssocByWhere('unit_structures', ['unit_structure_id', 'title'], ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    /**
     * Select assoc array by where
     *
     * @access public
     * @return array
     * @throws Exception
     */
    public function selectAssocByWhere() {
        return $this->fetchAssocByWhere('unit_structures', ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    /**
     * Select specified fields of records by where
     *
     * @access public
     * @return array
     * @throws Exception
     */
    public function selectFieldsAssocByWhere() {
        return $this->fetchFieldsAssocByWhere('unit_structures', ['unit_structure_id', 'title'], ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    /**
     * Insert data
     *
     * @access public
     * @return bool
     * @throws Exception
     */
    public function insertData() {
        return $this->insert(
            'unit_structures',
            [
                'title' => 'Test',
                'description' => 'Some desc',
                'data_structures' => '{}'
            ],
            'unit_structure_id'
        );
    }

    /**
     * Insert batch data
     *
     * @access public
     * @return array|bool
     * @throws Exception
     */
    public function insertBatchData() {

        return $this->insertBatch(
            'unit_structures',
            ['title', 'description', 'data_structures'],
            [
                [
                    'title1', 'description1', '{"a":1}'
                ],
                [
                    'title2', 'description2', '{"a":2}'
                ],
                [
                    'title3', 'description3', '{"a":3}'
                ]
            ],
            'unit_structure_id'
        );

    }

    /**
     * Update data
     *
     * @access public
     * @return int
     * @throws Exception
     */
    public function updateData() : int {
        return $this->update('unit_structures', ['data_structures' => '{"test":"abc_'.time().'"}'], ['title' => 'title3']);
    }

    /**
     * Delete data
     *
     * @access public
     * @return int
     * @throws Exception
     */
    public function deleteData() : int {
        return $this->delete('unit_structures', ['title' => 'Test']);
    }

    /**
     * Test query with affected rows
     *
     * @access public
     * @return array
     * @throws Exception
     */
    public function testQueryWithAffectedRows() : array {
        
        $result = [];

        $result['query1'] = $this->queryWithAffectedRows("select * from unit_Structures where title = $1", ['update_this']);
        $result['query2'] = $this->queryWithAffectedRows("update unit_Structures set data_structures = $1 where title = $2", ['{"test":"abc_'.time().'"}', 'update_this']);
        
        return $result;
    }

    /**
     * Transactions testing
     *
     * @access public
     * @return string
     */
    public function testTransactions() : string {
        
        $this->beginTrans();

        try {
            
            // Execute queries
            $this->query("update unit_Structures set data_structures = $1 where title = $2", ['{"test":"abcs_name_'.time().'","num":999}', 'update_this']);
            $this->query("update unit_Structures set data_structures = $1 where title_invalid_field_name = $2", ['{"test":"abc_'.time().'"}', 'update_this']);
            
            // Commit transaction
            $this->commitTrans();
            return 'This should not work';
            
        } catch(\Throwable $e) {

            // Rollback transaction in case if any of queries execution fail
            $this->rollbackTrans();
            return 'OK Trans Rollback!';
            
        }

    }

    /**
     * Test the query error
     *
     * @access public
     * @return array|string[]
     */
    public function testQueryError(): array
    {

        try {

            // This should trigger a warning
            // The warning should catch by the system and throw an Exception
            $this->query("update unit_Structures set data_structures = $1 where title_invalid_field_name = $2", ['{"test":"abc_'.time().'"}', 'update_this']);

        } catch (Throwable $e) {
            
            return [
                'we_have_catch_the_query_exception_as' => $e->getMessage()
            ];
        }

        return [
            'nothing_to_catch' => 'OK'
        ];
    }
}