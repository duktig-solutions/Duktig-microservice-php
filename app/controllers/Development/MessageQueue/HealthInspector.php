<?php
/**
 * Message/Queue Health inspector Class
 *
 * Usage: php ~/Sites/duktig.microservice.1/cli/exec.php development-mq-consumer-health-inspector --redis-config MessageQueue
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Development\MessageQueue;

use System\CLI\Input;
use System\CLI\Output;
use System\Config;
use System\Logger;
use System\MessageQueue\HealthInspector as HealthInspectorProcess;

    /**
 * Class HealthInspector
 *
 * @package App\Controllers\MessageQueue
 */
class HealthInspector {

    /**
     * Main inspector method
     *
     * @param Input $input
     * @param Output $output
     * @param array $middlewareResult
     */
    public function inspect(Input $input, Output $output, array $middlewareResult) : void {

        try {
            $redisConfigName = $input->parsed('redis-config');

            if (!$redisConfigName) {
                throw new \Exception('redis-config required!');
            }

            $config = Config::get()['Redis'][$redisConfigName];

            if (!$config) {
                throw new \Exception('Cannot find redis configuration by ' . $redisConfigName);
            }

            HealthInspectorProcess::inspect($config);

        } catch (\Throwable $e) {
            Logger::Log($e->getMessage(), Logger::ERROR, null, null, 'mq-producer.log');
            $output->stderr($e->getMessage());
        }

    }

}