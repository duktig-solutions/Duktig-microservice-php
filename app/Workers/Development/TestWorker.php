<?php
/**
 * TestWorker For Message/Queue Consumer.
 * A Consumer receives tasks from Redis and calls workers
 * 
 * Functionality steps explained.
 *
 * 1. A producer pushes a message to Redis Server.
 * 2. Consumer: 
 *      The controller: app/controllers/MessageQueue/Consumer.php 
 *      loaded the consumer class: kernel/system/classes/MessageQueue/Consumer.php
 *      which will catch messages/tasks from Redis and call this Worker class.
 *
 *      Final Results will report to Consumer class method.
 *
 * Difference between Events and Message Queue is not only the unique task workers.
 * Each message comes to message queue already formatted as a task/action.
 * So, the message in Message/Queue should be formatted as:
 *
 * {
 *     "workerTask": "Development\TestWorker->Task1",
 *     "parameters":{
 *         "param1": "value1",
 *         "param2": [1,2,3,4,5,6]
 *     }
 * }
 *
 * Log Example: 93:358050932:358050932:16987:Task2
 * Log Format: {taskId}:{expected-result}:{task-result}:{consumer-pid}:{task-method}
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Workers\Development;

/**
 * Class TestWorker
 *
 * @package App\Workers
 */
Class TestWorker {

    private $logFile;
    private $pid;

    public function __construct() {
        $this->logFile = DUKTIG_APP_PATH . 'log/mq-analyze.txt';
        $this->pid = getmypid();
    }

    /**
     * This is a Worker task method which will be called by consumer.
     *
     * @access public
     * @param array $parameters
     * @return bool
     */
    public function Task1(array $parameters) : bool {

        echo "Start: ";

        $result = array_sum($parameters['numbers']);

        $resultStr = $parameters['taskId'].':'.$parameters['taskNumber'].':'.$parameters['expecting'].':'.$result.':'.$this->pid.':'.__FUNCTION__;

        $this->log($resultStr);

        echo $resultStr . "\n";

        return True;
    }

    public function Task2(array $parameters) : bool {

        echo "Start: ";

        $result = array_sum($parameters['numbers']) - 10 - $parameters['taskNumber'];

        $resultStr = $parameters['taskId'].':'.$parameters['taskNumber'].':'.$parameters['expecting'].':'.$result.':'.$this->pid.':'.__FUNCTION__;

        $this->log($resultStr);

        sleep(mt_rand(1, 3));

        echo $resultStr . "\n";

        return True;
    }

    public function Task3(array $parameters) : bool {

        echo "Start: ";

        $r = mt_rand(1, 100);

        if($r <= 15) {
            echo "Fail!\n";
            return false;
        }

        $result = $parameters['numbers'][0] + $parameters['taskNumber'] + $parameters['numbers'][1] - $parameters['numbers'][2];

        $resultStr = $parameters['taskId'].':'.$parameters['taskNumber'].':'.$parameters['expecting'].':'.$result.':'.$this->pid.':'.__FUNCTION__;

        echo $resultStr . "\n";

        usleep(mt_rand((int) 1000000 / 4, (int) 1000000 / 2));

        $this->log($resultStr);

        return True;
    }

    private function Log($resultStr) {
        file_put_contents($this->logFile, $resultStr."\n", FILE_APPEND);
    }
}