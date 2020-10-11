<?php
/**
 * Message/Queue Testing Producer
 *
 * Usage: php ~/Sites/duktig.microservice.1/cli/exec.php mq-producer-test --redis-config MessageQueue
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\MessageQueue;

use System\Input;
use System\Output;
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
    private $logFile = DUKTIG_APP_PATH . 'log/mq-analyze.txt';

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
            $workerTask = "TestWorker->Task".$worker;
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

            $message = json_encode([
                'workerTask' => $workerTask,
                'parameters' => [
                    'taskNumber' => $taskNumber,
                    'numbers' => $numbers,
                    'expecting' => $expectingResult
                ]
            ]);

            echo $i . ' : '  . $expectingResult . "\n";

            $redis->lPush($config['queueName'], $message);
            $i++;

            // 1 second = 1000000 microsecond
            usleep(250000); // 0.25 Second
            //sleep(1);
        }
    }

}