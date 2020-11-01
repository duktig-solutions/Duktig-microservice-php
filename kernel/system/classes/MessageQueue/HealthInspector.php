<?php
/**
 * Message/Queue Consumers Health checker Class
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

class HealthInspector {

    private const NO_WORKERS_IN_HEARTBEAT = -1;

    private static $taskQueue;
    private static $heartBeatList;
    private static $heartBeatExpireMinutes = 5;

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
     * Consumer log file name
     *
     * @static
     * @access private
     * @var string
     */
    private static $logFile = 'mq-consumer.log';

    public static function inspect($config) {

        static::$config = $config;

        static::$taskQueue = $config['queueName'];
        static::$heartBeatList = $config['queueName'].':workers-heartbeat';

        static::$redis = new Redis();

        static::$redis->connect($config['host'], $config['port'], 0);

        if ($config['password'] != '') {
            static::$redis->auth($config['password']);
        }

        // Possible cases to clean up.

        // 1. Worker queue exists by worker id, but worker doesn't exists in heartbeat list.
        // 2. Worker queue exists by worker id, worker exists in heartbeat list, but the exec time expired.
        // 3. Worker exists in heartbeat list but worker queue not exists
        // 4. Workers count in heartbeat list not match with required workers count. someone died.

        while(True) {

            // This is not required, because not always workers amount matches with heartbeat check list.
            // static::inspectHeartBeatAndWorkersQueueMatch();
            static::inspectHeartBeat();
            static::inspectWorkersQueue();

            sleep(5);
        }

    }

    private static function getQueueCount() {
        return static::$redis->lLen(static::$taskQueue);
    }

    private static function getWorkersCountFromHeartBeat() {
        return static::$redis->lLen(static::$heartBeatList);
    }

    private static function getWorkersFromHeartBeat() {
        return static::$redis->lRange(static::$heartBeatList, 0, -1);
    }

    private static function getWorkersCountFromWorkersQueueList() {

        $workers = static::$redis->keys(static::$taskQueue . ':worker:*');

        if(!$workers) {
            return 0;
        }

        return count($workers);
    }

    private static function getWorkersQueueCountFromWorkersQueueList() {

        $workers = static::$redis->keys(static::$taskQueue . ':worker:*');

        if(!$workers) {
            return [];
        }

        $result = [];

        foreach($workers as $workerList) {
            $result[$workerList] = static::$redis->lLen($workerList);
        }

        return $result;
    }

    private static function getWorkersFromWorkersQueueList() {
        return static::$redis->keys(static::$taskQueue . ':worker:*');
    }

    private static function getWorkerTasksFromWorkersQueueList($workerId) {
        return static::$redis->lRange(static::$taskQueue.':worker:'.$workerId, 0, -1);
    }

    private static function getWorkerTasksQueueCountFromWorkersQueueList($workerId) {
        return static::$redis->lLen(static::$taskQueue.':worker:'.$workerId);
    }

    private static function getDbSize() {
        return static::$redis->dbSize();
    }

    public static function inspectHeartBeat() {

        $workers = static::getWorkersFromHeartBeat();

        if(empty($workers)) {
            return static::NO_WORKERS_IN_HEARTBEAT;
        }

        foreach($workers as $worker) {

            $tmp = explode(':', $worker);

            # Remove the worker, if the key is incorrect
            if(count($tmp) != 2) {
                static::log('Incorrect worker defined in heartBeat list: '.$worker);
                //static::$redis->lRem(static::$heartBeatList, $worker, 1);
                continue;
            }

            $workerId = $tmp[0];
            $lastHeartBeat = $tmp[1];

            if(!static::checkHeartBeatTime($lastHeartBeat)) {
                static::log('Worker: ' . $workerId . ' heartBeat time expired.');
                static::moveWorkerQueueTasksToMainQueue($workerId);
                static::$redis->lRem(static::$taskQueue . ':workers-heartbeat', $worker, 1);
            }
        }

    }

    public static function inspectWorkersQueue() {

        $workers = static::getWorkersFromWorkersQueueList();

        if(empty($workers)) {
            // This can be empty, because of no new tasks.
            // static::log('No workers in Workers queue list.');
            return false;
        }

        foreach($workers as $worker) {

            # Check heartbeat check exists
            # MQ_d876g66886gfd:worker:7099
            $tmp = explode(':', $worker);
            $workerId = $tmp[2];

            if(!static::heartBeatExists($workerId)) {
                static::log('Worker Queue exists but heart beat not: '.$workerId);
                static::moveWorkerQueueTasksToMainQueue($workerId);
            }
        }
    }

    private static function inspectHeartBeatAndWorkersQueueMatch() {

        $taskQueueWorkers = static::getWorkersCountFromWorkersQueueList();
        $heartBeatWorkers = static::getWorkersCountFromHeartBeat();

        if($taskQueueWorkers != $heartBeatWorkers) {
            static::log('Workers amount in heartBeat : '.$heartBeatWorkers.' not matches with workers Queue list: '.$taskQueueWorkers);
            return False;
        }

        return True;
    }

    private static function checkHeartBeatTime($lastHeartBeat) {

        $lastHeartBeat = (int) $lastHeartBeat;

        if (time() - $lastHeartBeat > static::$heartBeatExpireMinutes * 60) {
            return False;
        }

        return True;
    }

    private static function moveWorkerQueueTasksToMainQueue($workerId) {

        $workerTasksCount = static::getWorkerTasksQueueCountFromWorkersQueueList($workerId);

        if($workerTasksCount < 1) {
            return false;
        }

        static::log('Moving '.$workerTasksCount.' tasks of worker '.$workerId.' to main queue');

        $task = True;

        while($task) {
            $task = static::$redis->rPopLPush(static::$taskQueue . ':worker:' . $workerId, static::$taskQueue);
        }

        return True;
    }

    private static function heartBeatExists($workerId) {

        $workers = static::getWorkersFromHeartBeat();

        if(empty($workers)) {
            return False;
        }

        foreach($workers as $workerTime) {
            $tmp = explode(':', $workerTime);
            $heartBeatWorkerId = $tmp[0];

            if((int) $heartBeatWorkerId == $workerId) {
                return True;
            }
        }

        return False;
    }

    public static function reports() {
        print_r([
            'getQueueCount' => static::getQueueCount(),
            'getWorkersCountFromHeartBeat' => static::getWorkersCountFromHeartBeat(),
            'getWorkersFromHeartBeat' => static::getWorkersFromHeartBeat(),
            'getWorkersCountFromWorkersQueueList' => static::getWorkersCountFromWorkersQueueList(),
            'getWorkersFromWorkersQueueList' => static::getWorkersFromWorkersQueueList(),
            'getWorkersQueueCountFromWorkersQueueList' => static::getWorkersQueueCountFromWorkersQueueList(),
            'dbSize' => static::getDbSize()
        ]);
    }

    private static function log($message) {
        Logger::Log($message, Logger::WARNING);
        echo "LOG: " . $message . "\n";
    }

}