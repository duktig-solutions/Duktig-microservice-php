<?php
/**
 * Message/Queue Consumer controller
 * This controller will receive a request from Command line and start the main Consumer class.
 *
 * Usage: php ~/Sites/duktig.microservice.1/cli/exec.php mq-consumer --redis-config MessageQueue
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\MessageQueue;

use System\Input;
use System\Output;
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
            Logger::Log($e->getMessage(), Logger::ERROR, null, null, 'mq-consumer.log');
        }
    }

}