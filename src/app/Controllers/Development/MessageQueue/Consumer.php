<?php
/**
 * Message/Queue Consumer controller
 * This controller will receive a request from Command line and start the main Consumer class.
 *
 * Usage: php ./src/cli/exec.php development-mq-consumer --redis-config MessageQueue
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
use System\MessageQueue\Consumer as TasksConsumer;

class Consumer {

    private array $messages = [];


    /**
     * Main Consumer Class
     *
     * @access public
     * @param Input $input
     * @param Output $output
     * @param array $middlewareResult
     * @return void
     * @throws \Throwable
     */
    public function consume(Input $input, Output $output, array $middlewareResult) : void
    {

        TasksConsumer::init(Config::get()['Redis']['MessageQueue']);
        TasksConsumer::run(function ($messages) {
            $this->processMessages($messages);
        }, 3);

    }

    private function checkAlert($destination, $value) {
        if($value > 95) {
            echo " !!! Alert: ". $destination . " : " . $value ."\n";
        }
    }

    private function processMessages($messages) {

        if(empty($messages)) {
            return;
        }

        $insert = [];

        echo " + processing " . count($messages) . "\n";

        foreach($messages as $messageStr) {

            $message = json_decode($messageStr, true);

            $destination = $message['destination'];
            $value = $message['value'];

            if(!isset($insert[$destination])) {
                $insert[$destination] = [];
            }

            //echo " ~ making insert array\n";
            $insert[$destination][] = $value;

            //echo " ~ checking alerts\n";
            $this->checkAlert($destination, $value);

        }

        foreach($insert as $destination => $records) {
            echo "_Insert into ".$destination." (values) ".implode(', ', $records)."\n";
            usleep(300000); // 0.3 second
        }

    }


}