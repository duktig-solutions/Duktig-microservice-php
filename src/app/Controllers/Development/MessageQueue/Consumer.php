<?php
/**
 * Message/Queue Consumer controller
 * This controller will receive a request from Command line and start the main Consumer class.
 *
 * Usage: php ./cli/exec.php development-mq-consumer --redis-config MessageQueue
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Development\MessageQueue;

use System\CLI\Input;
use System\CLI\Output;
use System\MessageQueue\Consumer as ConsumerProcess;
use System\Config;
use System\Logger;

class Consumer {

    /**
     * Main Consumer Class
     *
     * @access public
     * @param Input $input
     * @param Output $output
     * @param array $middlewareResult
     * @return void
     */
    public function consume(Input $input, Output $output, array $middlewareResult) : void {

        try {
            $redisConfigName = $input->parsed('redis-config');

            if (!$redisConfigName) {
                throw new \Exception('redis-config required!');
            }

            $config = Config::get()['Redis'][$redisConfigName];

            if (!$config) {
                throw new \Exception('Cannot find redis configuration by ' . $redisConfigName);
            }

            ConsumerProcess::init($config);

        } catch (\Throwable $e) {
            Logger::Log($e->getMessage(), Logger::ERROR, null, null, 'development-mq-consumer.log');
        }
    }

}