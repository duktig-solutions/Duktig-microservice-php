<?php
/**
 * Message/Queue Consumer Class
 * A Consumer receives tasks from Redis and calls workers
 *
 * Works with Redis Database List.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 2.1.1
 * @requires phpredis extension
 */
namespace System\MessageQueue;

use Exception;
use \Redis;
use RedisException;
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
    private static array $config;

    /**
     * Connected status
     *
     * @static
     * @access private
     * @var bool
     */
    private static bool $connected = false;

    /**
     * Redis object
     *
     * @static
     * @access private
     * @requires phpredis extension
     * @var Redis object
     */
    private static Redis $redis;

    /**
     * Task Queue name to get messages
     *
     * @static
     * @access private
     * @var string
     */
    private static string $taskQueue;

    /**
     * @static
     * @access private
     * @var array
     */
    private static array $messages = [];

    /**
     * Main initialization class
     *
     * @static
     * @access private
     * @param array $config
     * @return bool
     * @throws RedisException
     */
    public static function init(array $config) : bool {

        sleep(2);

        Logger::Log('Initializing MessageQueueConsumer for: '.$config['queueName'], Logger::INFO, __FILE__, __LINE__);

        static::$config = $config;
        static::$taskQueue = $config['queueName'];

        static::$redis = new Redis();

        Logger::Log('Connecting to Redis Database for: '.$config['queueName'], Logger::INFO, __FILE__, __LINE__);

        $step = 1;
        static::$connected = false;

        while(static::$connected == false) {

            try {
                static::$redis->connect($config['host'], $config['port']);
                static::$connected = true;
                Logger::Log('Connected to Redis Database for: '.$config['queueName'].' successfuly.', Logger::INFO, __FILE__, __LINE__);
            } catch(Throwable $e) {
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
     * Run the Message queue listening, catching, executing functionality.
     * This method will catch a message from the message queue, move to his own list.
     * After an execution message will be deleted.
     *
     * @static
     * @access public
     * @param callable $callback
     * @param int|null $interval
     * @return void
     */
    public static function run(callable $callback, ?int $interval = 0) : void {

        Logger::Log('Starting Consumer for '.static::$taskQueue.' ...', Logger::INFO, __FILE__, __LINE__);

        $lastCalledBack = time();

        # Loop to catch a message and execute.
        # If there are no messages, this will wait 0.5 seconds.
        while(True) {

            try {

                # Catch a message from the list
                $message = static::$redis->lPop(static::$taskQueue);

                if(!empty($message)) {
                    static::$messages[] = $message;
                }

            } catch(Throwable $e)  {
                Logger::log($e->getCode() . ' _ ' . $e->getMessage(), Logger::WARNING, $e->getFile(), $e->getLine());
                $message = NULL;
                echo "Error: ".$e->getMessage() ."\n";
                sleep(1);
            }


            if (time() - $lastCalledBack >= $interval and !empty(static::$messages)) {
                $callback(static::$messages);
                static::$messages = [];
                $lastCalledBack = time();
            }

            usleep(1);

        }

    }

    /**
     * Execute Task
     *
     * @static
     * @access public
     * @param string $message
     * @param int $workerId
     * @return array
     * @throws Exception
     * @todo finalize this method
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
                            [uid] => ecdd2559-9e05-4af3-b4e4-f1154a32d792
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
            $parameters = $workerData['parameters'] ?? [];

            # Create new Worker Object and execute
            $workerObject = new $className();

            if(!$workerObject->$methodName($parameters)) {
                $workerData['status'] = 'fail';
                throw new Exception('Worker task `'.$workerData['workerTask'].'` not returned true.');
            }

            # Work complete!
            $workerData['status'] = 'ok';

        } catch(Throwable $e) {

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
                $attempted_message = json_encode($workerData, JSON_NUMERIC_CHECK);

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
            throw new Exception('Worker task not returned actual result. Message: '.$message.'. Consumer: '.static::$taskQueue, Logger::ERROR);
        }

        return $workerData;

    }

    /**
     * @param string $message
     * @return array
     * @todo finalize this
     */
    public static function validateTask(string $message) : array {
        return json_decode($message, true);
    }

}