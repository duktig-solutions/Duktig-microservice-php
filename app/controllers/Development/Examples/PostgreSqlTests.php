<?php
namespace App\Controllers\Development\Examples;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;
use App\Models\Examples\PostgreSqlTestModel as PgModel;

/**
 * Class To test PostgreSQL Functionality
 *
 * @package App\Controllers\Examples
 */
class PostgreSqlTests {
    
    public function run(Request $request, Response $response, array $middlewareData) : void {
        
        $model = new PgModel();
        //$result = $model->selectAllAssoc();
        //$result = $model->selectAssoc();
        //$result = $model->selectAllAssocByWhere();
        //$result = $model->selectAllFieldsAssocByWhere();
        //$result = $model->selectAssocByWhere();
        //$result = $model->selectFieldsAssocByWhere();
        //$result = $model->insertData();
        //$result = $model->insertBatchData();
        //$result = $model->updateData();
        //$result = $model->deleteData();
        //$result = $model->testQueryWithAffectedRows();
        $result = $model->testTransactions();
        //$result = $model->testQueryError();

        $response->sendJson([
            'result' => $result
        ]);
    }

}