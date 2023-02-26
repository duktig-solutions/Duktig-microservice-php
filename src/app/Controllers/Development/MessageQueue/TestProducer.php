<?php
/**
 * Message/Queue Testing Producer
 * A Producer pushes a tasks to Redis
 *
 * Usage: php ./cli/exec.php development-mq-producer-test --redis-config MessageQueue
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Development\MessageQueue;

use System\CLI\Input;
use System\CLI\Output;
use System\Config;
use System\Logger;
use \Redis;

class TestProducer {

    /**
     * Log file
     *
     * @access private
     * @var string
     */
    private string $logFile = DUKTIG_APP_PATH . 'log/mq-analyze.txt';
    private string $logFileFailTasks = DUKTIG_APP_PATH . 'log/mq-fail-tasks.log';

    /**
     * Main producing method
     *
     * @param Input $input
     * @param Output $output
     * @param array $middlewareResult
     */
    public function produce(Input $input, Output $output, array $middlewareResult) : void {

        ini_set("default_socket_timeout", '-1');

        # Reset Log File
        file_put_contents($this->logFile, '');
        file_put_contents($this->logFileFailTasks, '');

        try {
            $redisConfigName = $input->parsed('redis-config');

            if (!$redisConfigName) {
                throw new \Exception('redis-config required!');
            }

            $config = Config::get()['Redis'][$redisConfigName];

            if (!$config) {
                throw new \Exception('Cannot find redis configuration by ' . $redisConfigName);
            }

            $this->run($config);

        } catch (\Throwable $e) {
            Logger::Log($e->getMessage(), Logger::ERROR, null, null, 'mq-producer.log');
            $output->stderr($e->getMessage());
        }
    }

    private function run($config) {

        $redis = new Redis();

        //$redis->pconnect('10.211.55.3', 6379);
        $redis->connect($config['host'], $config['port'], 0);

        if ($config['password'] != '') {
            $redis->auth($config['password']);
        }

        $i = 1;

        while (true) {

            $worker = mt_rand(1, 3);
            $workerTask = "Development\TestWorker->Task".$worker;
            $taskNumber = $i;
            $numbers = [
                mt_rand(1, 100),
                mt_rand($i * 3 + mt_rand(2, 20), $i * 3 + mt_rand(2, 20) + 50000000) * 10 + mt_Rand(3, 90),
                mt_rand(0, $i + 56)
            ];

            $expectingResult = 0;

            if($worker == 1) {
                $expectingResult = array_sum($numbers);
            } elseif($worker == 2) {
                $expectingResult = array_sum($numbers) - 10 - $i;
            } elseif($worker == 3) {
                $expectingResult = $numbers[0] + $i + $numbers[1] - $numbers[2];
            }

            $taskId = mt_rand(10, 99).uniqid().mt_rand(10, 99).str_pad($i, 8, '0', STR_PAD_LEFT);

            $message = json_encode([
                'workerTask' => $workerTask,
                'parameters' => [
                    'taskNumber' => $taskNumber,
                    'numbers' => $numbers,
                    'expecting' => $expectingResult,
                    'taskId' => $taskId
                ],
                'taskId' => $taskId
            ]);

            echo $taskId . ' : ' . $i . ' : '  . $expectingResult . "\n";

            $redis->lPush($config['queueName'], $message);
            $i++;

            // 1 second = 1000000 microsecond
            usleep(250000); // 0.25 Second
            //sleep(1);
        }
    }

}