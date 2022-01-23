<?php
/**
 * Message/Queue Consumer Class
 * A Consumer receives tasks from Redis and calls workers
 *
 * Works with Redis Database List.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 2.1.0
 * @requires phpredis extension
 */
namespace System\MessageQueue;

use Exception;
use \Redis;
use System\Config;
use System\Logger;
use Throwable;

class Consumer {

    /**
     * Configuration
     *
     * @static
     * @access private
     * @var array
     */
    private static $config;

    /**
     * Connected status
     *
     * @static
     * @access private
     * @var bool
     */
    private static $connected = false;

    /**
     * Redis object
     *
     * @static
     * @access private
     * @requires phpredis extension
     * @var Redis object
     */
    private static $redis;

    /**
     * Task Queue name to get messages
     *
     * @static
     * @access private
     * @var string
     */
    private static $taskQueue;

    /**
     *
     * @static
     * @access private
     * @var string $workerId
     */
    private static $workerId;

    /**
     * Last Beat timestamp
     *
     * @static
     * @access private
     * @var int $lastBeat
     */
    private static $lastBeat;

    /**
     * Main initialization class
     *
     * @static
     * @access private
     * @param array $config
     * @return bool
     */
    public static function init(array $config) : bool {

        sleep(2);

        Logger::Log('Initializing MessageQueueConsumer for: '.$config['queueName'], Logger::INFO, __FILE__, __LINE__);

        static::$config = $config;
        static::$taskQueue = $config['queueName'];
        static::$workerId = getmypid();

        static::$redis = new Redis();

        Logger::Log('Connecting to Redis Database for: '.$config['queueName'], Logger::INFO, __FILE__, __LINE__);

        $step = 1;
        static::$connected = false;

        while(static::$connected == false) {

            try {
        static::$redis->connect($config['host'], $config['port'], 0);
                static::$connected = true;
                Logger::Log('Connected to Redis Database for: '.$config['queueName'].' successfuly.', Logger::INFO, __FILE__, __LINE__);
            } catch(\Throwable $e) {
                Logger::log('Retrying to connect Redis... ' . $step, Logger::INFO, __FILE__, __LINE__);
                $step++;
                sleep(1);
            }

        }

        if ($config['password'] != '') {
            static::$redis->auth($config['password']);
        }

        # Select the database
        static::$redis->select($config['database']);

        return True;
    }

    /**
     * Last Beat, like a ping to Redis list: {message_Queue}:workers-heartbeat
     *    {workerId}:{lastTimeStamp}
     *
     * With this method, we always informing our last heart beat. When was the last?!
     *
     * @static
     * @access private
     * @return bool
     */
    private static function heartBeat() : bool {

        if(!static::$connected) {
            Logger::log('Trying to heartBeat but still not connected!', Logger::ERROR, __FILE__, __LINE__);
            return false;
        }

        try {
        static::$redis->lRem(static::$taskQueue . ':workers-heartbeat', static::$workerId.':'.static::$lastBeat, 1);
        static::$lastBeat = time();
        static::$redis->lPush(static::$taskQueue . ':workers-heartbeat', static::$workerId.':'.static::$lastBeat);
            return true;
        } catch(\Throwable $e) {
            Logger::log($e->getMessage(), Logger::ERROR, $e->getFile(), $e->getLine());
            return false;
        }

    }

    /**
     * Validate a task and return decoded array from json
     *
     * @access public
     * @param string $message
     *
     */
    public static function validateTask(string $message) {

        # First, let's extract the worker data
        try {

            $task = json_decode($message, true);

            # If the task is not correct json, no make sense to continue.
            if(!$task) {
                throw new \Exception('Invalid Task Content (Not a json string): ' . $message);
            }

            if(!isset($task['workerTask'])) {
                throw new \Exception('Invalid Task Content (workerTask not set): ' . $message);
            }

            $workerExecutable = explode('->', $task['workerTask']);

            if(count($workerExecutable) < 2) {
                throw new \Exception('Invalid Task Content (invalid workerTask `'.$task['workerTask'].'`): ' . $message);
            }

            $task['class'] = $workerExecutable[0];
            $task['method'] = $workerExecutable[1];

            # Because there is no error, the task status is 'ok'
            $task['status'] = 'ok';

            return $task;

        } catch (\Throwable $e) {

            # The status error assuming that this task will never repeat.
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Run the Message queue listening, catching, executing functionality.
     * This method will catch message from message queue, move to his own list.
     * After execution message will be deleted.
     *
     * @static
     * @access public
     * @return void
     */
    public static function run() {
        
        Logger::Log('Starting Consumer for '.static::$taskQueue.' ...', Logger::INFO, __FILE__, __LINE__);

        # Loop to catch a message and execute.
        # If there are no message, this will wait 0.5 second.
        while(True) {

            try {
            # Catch a message if any and move to own list.
            $message = static::$redis->rPopLPush(static::$taskQueue, static::$taskQueue . ':worker:' . static::$workerId);
            } catch(\Throwable $e)  {
                Logger::log($e->getCode() . ' _ ' . $e->getMessage(), Logger::WARNING, $e->getFile(), $e->getLine());
                $message = NULL;
                sleep(1);
            }

            if($message) {
                
                # Debug
                # echo $message . "\n";

                try {

                    # Command template: php source_code.php {json_encoded_message} {worker_id} > /dev/null 2>&1 &
                    $cmd = '' . Config::get()['Executables']['php'] . " /src/cli/exec.php runWorker '" . $message . "' ".static::$workerId." > /dev/null 2>&1 & ";

                    # Debug
                    # Logger::log($cmd, Logger::INFO, __FILE__, __LINE__);

                    # Run the worker process in background mode.
                    exec($cmd);

                    # From now this process is free. The 'runWorker' started to work in background mode and care about task execution.

                } catch (\Throwable $e) {
                    Logger::Log($e->getMessage(), Logger::ERROR, $e->getFile(), $e->getLine());
                    # echo $e->getMessage() . "\n";
                }

            } else {

                # Let's wait for 0.5 Second
                usleep(1000000 / 2);

            }

            static::heartBeat();
        }

    }

    /**
     * Execute Task
     *
     * @static
     * @access public
     * @param string $message
     * @return array
     */
    public static function execute(string $message, int $workerId) : array {

        $workerData = static::validateTask($message);

        # Example of Validated task:
        /*
        (
            [workerTask] => AccountsAuth->defineSetLocationByIp
            [parameters] => Array
                (
                    [service] => Accounts.Auth
                    [time] => 2021-08-24 22:24:15
                    [event] => user_signin_success
                    [data] => Array
                        (
                            [userId] => CUQr8bL7TGAyjG6ifOoWjUKBsHJOIu
                            [email] => james56@example.com
                            [ip_address] => 192.168.0.109
                        )

                )

            [taskId] => 1629831271
            [status] => ok
            [attempts] => 1
            [class] => AccountsAuth
            [method] => defineSetLocationByIp
        )
        */

        # Now trying to execute the task.
        try {

            # In case if something wrong with task validation
            if($workerData['status'] == 'error') {
                throw new Exception($workerData['message']);
            }

            # Define attempts if not defined
            $workerData['attempts'] = isset($workerData['attempts']) ? $workerData['attempts'] + 1 : 1;

            # Class name and method name set by validator method
            $className = "\\App\\Workers\\".$workerData['class'];
            $methodName = $workerData['method'];

            # Define parameters if not empty
            $parameters = isset($workerData['parameters']) ? $workerData['parameters'] : [];

            # Create new Worker Object and execute
            $workerObject = new $className();

            if(!$workerObject->$methodName($parameters)) {
                $workerData['status'] = 'fail';
                throw new \Exception('Worker task `'.$workerData['workerTask'].'` not returned true.');
            }

            # Work complete!
            $workerData['status'] = 'ok';

        } catch(\Throwable $e) {

            if($workerData['status'] == 'ok') {
                $workerData['status'] = 'error';
            }

            $workerData['message'] = $e->getMessage();
        }

        # error status: task will never attempt again and should be removed.
        if($workerData['status'] == 'error') {

            static::$redis->lRem(static::$taskQueue . ':worker:' . $workerId, $message, 1);
            Logger::Log('Unable to execute worker task. Message: '.$workerData['message'].'. Queue Message: `'.$message.'`. Queue name: '.static::$taskQueue.'. This message/task will be deleted from Redis.', Logger::ERROR, __FILE__, __LINE__);

        # fail status: task will attempt again until reach the attempts limit.
        } elseif($workerData['status'] == 'fail') {

            # We have to check, if this task execution reached attempts limit, then we have to remove this task.
            if($workerData['attempts'] >= static::$config['task_execution_attempts']) {

                static::$redis->lRem(static::$taskQueue . ':worker:' . $workerId, $message, 1);
                Logger::Log('Worker task reached attempts limit : TaskId: '.$workerData['taskId'].'. Message: '.$workerData['message'].'. Queue Message: `'.$message.'`. Queue name: '.static::$taskQueue.'. This message will be deleted from Redis.', Logger::ERROR, __FILE__, __LINE__);

            # Let's move this task again to main queue
            } else {

                # We have to take last attempted message with attempts count
                unset($workerData['status']);
                $attempted_message = json_encode($workerData);

                # Removing the message from Consumer list
                static::$redis->lRem(static::$taskQueue . ':worker:' . $workerId, $message, 1);

                # Adding attempted message to main queue
                static::$redis->lPush(static::$taskQueue, $attempted_message);

                Logger::Log($workerData['message'] .'. Worker task execution is failed and will be moved back to main Queue. Message: '.$attempted_message, Logger::ERROR, __FILE__, __LINE__);
            }

        # ok status: The task is complete, we can remove this task.
        } elseif($workerData['status'] == 'ok') {
            static::$redis->lRem(static::$taskQueue . ':worker:' . $workerId, $message, 1);
            Logger::Log('Complete Worker Task! Removing from task queue: ' . static::$taskQueue . ' : Id: ' . $workerId, Logger::INFO, __FILE__, __LINE__);
        } else {
            throw new \Exception('Worker task not returned actual result. Message: '.$message.'. Consumer: '.static::$taskQueue, Logger::ERROR);
        }

        return $workerData;

     }

}