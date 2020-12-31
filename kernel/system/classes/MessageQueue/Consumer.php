<?php
/**
 * Message/Queue Consumer Class
 * A Consumer receives tasks from Redis and calls workers
 *
 * Works with Redis Database List.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 * @requires phpredis extension
 */
namespace System\MessageQueue;

use \Redis;
use System\Logger;

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
     * Consumer log file name
     *
     * @static
     * @access private
     * @var string
     */
    private static $logFile = 'mq-consumer.log';

    /**
     * Main initialization class
     *
     * @static
     * @access private
     * @param array $config
     * @return bool
     */
    public static function init(array $config) : bool {

        Logger::Log('Initializing MessageQueueConsumer for: '.$config['queueName'], Logger::INFO, null, null, static::$logFile);

        static::$config = $config;
        static::$taskQueue = $config['queueName'];
        static::$workerId = getmypid();

        static::$redis = new Redis();

        Logger::Log('Connecting to Redis Database for: '.$config['queueName'], Logger::INFO, null, null, static::$logFile);

        static::$redis->connect($config['host'], $config['port'], 0);

        if ($config['password'] != '') {
            static::$redis->auth($config['password']);
        }

        # Now Run the message queue listening, catching executing process!
        static::run();

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
     * @return void
     */
    private static function beat() : void {

        static::$redis->lRem(static::$taskQueue . ':workers-heartbeat', static::$workerId.':'.static::$lastBeat, 1);
        static::$lastBeat = time();
        static::$redis->lPush(static::$taskQueue . ':workers-heartbeat', static::$workerId.':'.static::$lastBeat);

    }

    /**
     * Run the Message queue listening, catching, executing functionality.
     * This method will catch message from message queue, move to his own list.
     * After execution message will be deleted.
     *
     * @static
     * @access private
     * @return void
     */
    private static function run() {

        Logger::Log('Starting Consumer for '.static::$taskQueue.' ...', Logger::INFO, null, null, static::$logFile);

        # Loop to catch a message and execute.
        # If there are no message, this will wait 0.5 second.
        while(True) {

            # Catch a message if any and move to own list.
            $message = static::$redis->rPopLPush(static::$taskQueue, static::$taskQueue . ':worker:' . static::$workerId);

            if($message) {

                try {

                    $result = static::execute($message);

                    # error status: task will never attempt again and should be removed.
                    if($result['status'] == 'error') {

                        static::$redis->lRem(static::$taskQueue . ':worker:' . static::$workerId, $message, 1);
                        Logger::Log('Unable to execute worker task. Message: '.$result['message'].'. Queue Message: `'.$message.'`. Queue name: '.static::$taskQueue.'. This message will be deleted.', Logger::ERROR, null, null, static::$logFile);

                        Logger::Log($result['taskId'], Logger::ERROR, null, null, 'mq-error-tasks.log');

                    # fail status: task will attempt again until reach the attempts limit.
                    } elseif($result['status'] == 'fail') {

                        # We have to check, if this task execution reached attempts limit, then we have to remove this task.
                        if($result['attempts'] >= static::$config['task_execution_attempts']) {

                            static::$redis->lRem(static::$taskQueue . ':worker:' . static::$workerId, $message, 1);
                            Logger::Log('Worker task reached attempts limit : TaskId: '.$result['taskId'].'. Message: '.$result['message'].'. Queue Message: `'.$message.'`. Queue name: '.static::$taskQueue.'. This message will be deleted.', Logger::WARNING, null, null, static::$logFile);

                        # Let's move this task again to main queue
                        } else {

                            # We have to take last attempted message with attempts count
                            unset($result['status']);
                            $attempted_message = json_encode($result);

                            # Removing the message from Consumer list
                            static::$redis->lRem(static::$taskQueue . ':worker:' . static::$workerId, $message, 1);

                            # Adding attempted message to main queue
                            static::$redis->lPush(static::$taskQueue, $attempted_message);

                            Logger::Log($result['message'] .'. Worker task moved to main Queue. Message: '.$attempted_message, Logger::WARNING, null, null, static::$logFile);
                        }

                        Logger::Log($result['taskId'], Logger::ERROR, null, null, 'mq-fail-tasks.log');

                    # ok status: The task is complete, we can remove this task.
                    } elseif($result['status'] == 'ok') {
                        static::$redis->lRem(static::$taskQueue . ':worker:' . static::$workerId, $message, 1);
                    } else {
                        throw new \Exception('Worker task not returned actual result. Message: '.$message.'. Consumer: '.static::$taskQueue, Logger::ERROR);
                    }

                } catch (\Throwable $e) {
                    Logger::Log($e->getMessage(), Logger::ERROR, null, null, static::$logFile);
                }

            } else {

                # Let's wait for 0.5 Second
                usleep(1000000 / 2);

            }

            static::beat();
        }

    }

    /**
     * Execute Task
     *
     * @static
     * @access private
     * @param $message
     * @return array
     */
    private static function execute(string $message) : array {

        # First, let's extract the worker data
        try {

            $workerData = json_decode($message, true);

            # If the task is not correct json no make sense to continue.
            if(!$workerData) {
                throw new \Exception('Invalid Task Content (Not a json string): ' . $message);
            }

            if(empty($workerData['workerTask'])) {
                throw new \Exception('Invalid Task Content (workerTask not set): ' . $message);
            }

            $workerConfig = explode('->', $workerData['workerTask']);

            if(count($workerConfig) < 2) {
                throw new \Exception('Invalid Task Content (invalid workerTask `'.$workerData['workerTask'].'`): ' . $message);
            }

        } catch (\Throwable $e) {

            # The status error assuming that this task will never repeat.
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        # Now trying to execute the task.
        try {

            # Define attempts if not defined
            $workerData['attempts'] = isset($workerData['attempts']) ? $workerData['attempts'] + 1 : 1;

            $className = "\\App\\Workers\\$workerConfig[0]";
            $methodName = $workerConfig[1];

            # Define parameters if not empty
            $parameters = isset($workerData['parameters']) ? $workerData['parameters'] : [];

            # Create new Worker Object and execute
            $workerObject = new $className();

            if(!$workerObject->$methodName($parameters)) {
                throw new \Exception('Worker task `'.$workerData['workerTask'].'` not returned true.');
            }

            # Work complete!
            $workerData['status'] = 'ok';

            return $workerData;

        } catch(\Throwable $e) {

            $workerData['status'] = 'fail';
            $workerData['message'] = $e->getMessage();

            return $workerData;

        }

    }

}