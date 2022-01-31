<?php
/**
 * PostgreSQL Testing Model
 * 
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Examples;

use System\Config;
use Throwable;

class PostgreSqlTestModel extends \Lib\Db\PostgreSQL {

    public function __construct() {
        $config = Config::get()['Databases']['DataWareHouse'];
        parent::__construct($config);
    }

    public function selectAllAssoc() {
        return $this->fetchAllAssoc("select * from unit_structures where title = $1", ['Server']);
    }

    public function selectAssoc() {
        return $this->fetchAssoc("select * from unit_structures where title = $1", ['Server']);
    }

    public function selectAllAssocByWhere() {
        return $this->fetchAllAssocByWhere('unit_structures', ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    public function selectAllFieldsAssocByWhere() {
        return $this->fetchAllFieldsAssocByWhere('unit_structures', ['unit_structure_id', 'title'], ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    public function selectAssocByWhere() {
        return $this->fetchAssocByWhere('unit_structures', ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

    public function selectFieldsAssocByWhere() {
        return $this->fetchFieldsAssocByWhere('unit_structures', ['unit_structure_id', 'title'], ['title' => 'Server', 'last_date' => '2022-01-24 20:37:17.065011']);
    }

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

    public function updateData() {
        return $this->update('unit_structures', ['data_structures' => '{"test":"abc_'.time().'"}'], ['title' => 'title3']);
    }

    public function deleteData() {
        return $this->delete('unit_structures', ['title' => 'Test']);
    }

    public function testQueryWithAffectedRows() {
        
        $result = [
            'query1' => null,
            'query2' => null
        ];

        $result['query1'] = $this->queryWithAffectedRows("select * from unit_Structures where title = $1", ['update_this']);
        $result['query2'] = $this->queryWithAffectedRows("update unit_Structures set data_structures = $1 where title = $2", ['{"test":"abc_'.time().'"}', 'update_this']);
        
        return $result;
    }

    public function testTransactions() {
        
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

    public function testQueryError() {

        try {
            // This should trigger a warning
            // The warning should be catch by the system and throw an Exception
            $this->query("update unit_Structures set data_structures = $1 where title_invalid_field_name = $2", ['{"test":"abc_'.time().'"}', 'update_this']);
        } catch (Throwable $e) {
            
            return [
                'we_have_catch_the_query_exception_as' => $e->getMessage()
            ];
        }
    }
}