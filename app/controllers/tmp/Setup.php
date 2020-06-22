<?php
/**
 * Setup Application
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers;

use \System\Input;
use \System\Output;

class Setup {

    private $config;

    public function __construct() {
        $this->config = \System\Config::get();
    }

    /**
     * Setup application
     *
     * @param Input $input
     * @param Output $output
     * @param array $middlewareResult
     */
    public function setupGeneral(Input $input, Output $output, array $middlewareResult) : void {

        # Setup Database
        $this->generateDatabaseData();

        $output->stdout('Done.');
    }

    public function generateDatabaseData() {
        $this->generateUsers();
    }

    protected function generateUsers() {

        error_reporting(E_ALL);

        $g = new \Lib\Generator();
        $model = new \App\Models\ExampleModel();

        // Cleanup the users table
        $model->query('truncate table users');

        // Create Root user account
        $rootAccount = $this->config['setup']['userAccounts']['rootAccount'];
        $rootAccount['password'] = \Lib\Auth\Password::encrypt($rootAccount['password']);

        $model->insert('users', $rootAccount);

        // Create Other accounts
        $createdCount = 0;
        $toCreateCount = $this->config['setup']['userAccounts']['generationCount'];

        while($createdCount < $toCreateCount) {

            $name = $g->createName(4, 5);
            $surname = $g->createName(5, 7);
            $email = $g->createEmail($name .'.'.$surname . '.'.$g->createName(5, 7).mt_rand(10, 100));

            // Check if email already not exists.
            if($model->query('select * from users where email = ?', [$email])->num_rows > 0) {
                continue;
            }

            $account = [
                'firstName' => $name,
                'lastName' => $surname,
                'email' => $email,
                'password' => sha1($g->createPassword(6, 8)),
                'phone' => $g->createNumber(8),
                'comment' => $g->createSentence(),
                'pinCode' => $g->createNumber(5),
                'dateRegistered' => $g->createDate('2015-05-15', date('Y-m-d')),
                'dateLastUpdate' => '',
                'dateLastLogin' => '',
                'roleId' => $g->createNumber(1),
                'status' => $g->createNumber(3)
            ];

            $model->insert('users', $account);

            $createdCount++;

        }

        return $createdCount;

    }

	/**
	 * Test Setup Environment
	 *
	 * @access public
	 * @param \System\Input $input
	 * @param \System\Output $output
	 * @param array $middlewareResult
	 * @return bool
	 */
    public function envTest(Input $input, Output $output, array $middlewareResult) : bool {

    	for($i = 0; $i <= 15; $i++) {
    		sleep(1);
    		$output->stdout($i);
	    }
	    $this->testDatabaseConnections($input, $output, $middlewareResult);

	    return true;

    }

	/**
	 * @param \System\Input $input
	 * @param \System\Output $output
	 * @param array $middlewareResult
	 * @throws \Exception
	 */
    protected function testDatabaseConnections(Input $input, Output $output, array $middlewareResult) {

	    # Test Database Connections
	    $output->stdout('Testing Database Connections');

	    $databaseConfig = \System\Config::get()['Databases'];

	    if(empty($databaseConfig)) {
		    $output->stdout('There are no Database Connections configuration.');
	    } else {
		    foreach ($databaseConfig as $dbConf) {

		    	$output->stdout('Connecting to ' . $dbConf['database'] . ' - ', false);
			    $modelLib = new \Lib\Db\MySQLiUtility($dbConf);

			    $tablesCount = count($modelLib->getTables());

			    $output->stdout($tablesCount . ' table(s) found.');
		    }
	    }

	    $output->stdout('Done.');

    }

}