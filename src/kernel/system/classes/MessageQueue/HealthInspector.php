<?php
/**
 * Message/Queue Consumers Health checker Class
 *
 * Works with Redis Database List.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.1.1
 * @requires phpredis extension
 */
namespace System\MessageQueue;

use \Redis;
use System\Logger;

/**
 * Class HealthInspector
 */
class HealthInspector {

    /**
     * @access private
     * @const int
     */
    private const NO_WORKERS_IN_HEARTBEAT = -1;

    /**
     * @static
     * @access private
     * @var string
     */
    private static string $taskQueue;

    /**
     * @static
     * @access private
     * @var string
     */
    private static string $heartBeatList;

    /**
     * @static
     * @access private
     * @var int
     */
    private static int $heartBeatExpireMinutes = 5;

    /**
     * Configuration
     *
     * @static
     * @access private
     * @var array
     */
    private static array $config;

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
     * Inspect
     *
     * @static
     * @access public
     * @param array $config
     * @return void
     */
    public static function inspect(array $config): void
    {

        static::$config = $config;

        static::$taskQueue = $config['queueName'];
        static::$heartBeatList = $config['queueName'].':workers-heartbeat';

        static::$redis = new Redis();

        static::$redis->connect($config['host'], $config['port']);

        if (static::$config['password'] != '') {
            static::$redis->auth($config['password']);
        }

        # Select the database
        static::$redis->select($config['database']);

        // Possible cases to clean up.

        // 1. Worker queue exists by worker id, but worker doesn't exist in heartbeat list.
        // 2. Worker queue exists by worker id, worker exists in heartbeat list, but the exec time expired.
        // 3. Worker exists in heartbeat list but worker queue not exists
        // 4. Workers count in heartbeat list not match with required workers count. someone died.

        while(True) {

            // This is not required, because not always workers amount matches with heartbeat check list.
            // static::inspectHeartBeatAndWorkersQueueMatch();
            static::inspectHeartBeat();
            static::inspectWorkersQueue();
            static::inspectTasksQueue();

            sleep(5);
        }

    }

    /**
     * Get Queue tasks count
     *
     * @static
     * @access private
     * @return bool|int|Redis
     */
    private static function getQueueCount(): bool|int|Redis
    {
        return static::$redis->lLen(static::$taskQueue);
    }

    /**
     * Get Workers count from heart beat
     *
     * @static
     * @access private
     * @return bool|int|Redis
     */
    private static function getWorkersCountFromHeartBeat(): bool|int|Redis
    {
        return static::$redis->lLen(static::$heartBeatList);
    }

    /**
     * Get workers from heart beat
     *
     * @static
     * @access private
     * @return array|Redis
     */
    private static function getWorkersFromHeartBeat(): array|Redis
    {
        return static::$redis->lRange(static::$heartBeatList, 0, -1);
    }

    /**
     * Get workers count from workers queue list
     *
     * @static
     * @access private
     * @return int
     */
    private static function getWorkersCountFromWorkersQueueList() : int {

        $workers = static::$redis->keys(static::$taskQueue . ':worker:*');

        if(!$workers) {
            return 0;
        }

        return count($workers);
    }

    /**
     * Get workers queue count from workers queue list
     *
     * @static
     * @access private
     * @return array
     */
    private static function getWorkersQueueCountFromWorkersQueueList() : array {

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

    /**
     * Get workers from workers queue list
     *
     * @static
     * @access private
     * @return array|Redis
     */
    private static function getWorkersFromWorkersQueueList(): array|Redis
    {
        return static::$redis->keys(static::$taskQueue . ':worker:*');
    }

    /**
     * Get worker tasks from workers queue list
     *
     * @static
     * @access private
     * @param string $workerId
     * @return array|Redis
     */
    private static function getWorkerTasksFromWorkersQueueList(string $workerId): array|Redis
    {
        return static::$redis->lRange(static::$taskQueue.':worker:'.$workerId, 0, -1);
    }

    /**
     * Get worker tasks queue count from workers queue list
     *
     * @static
     * @access private
     * @param $workerId
     * @return bool|int|Redis
     */
    private static function getWorkerTasksQueueCountFromWorkersQueueList($workerId): bool|int|Redis
    {
        return static::$redis->lLen(static::$taskQueue.':worker:'.$workerId);
    }

    /**
     * Get Database size
     *
     * @static
     * @access private
     * @return int|Redis
     */
    private static function getDbSize(): int|Redis
    {
        return static::$redis->dbSize();
    }

    /**
     * Inspect heart beat
     *
     * @static
     * @access private
     * @return int|void
     */
    public static function inspectHeartBeat() {

        $workers = static::getWorkersFromHeartBeat();

        if(empty($workers)) {
            return static::NO_WORKERS_IN_HEARTBEAT;
        }

        foreach($workers as $worker) {

            $tmp = explode(':', $worker);

            # Remove the worker, if the key is incorrect
            if(count($tmp) != 2) {
                Logger::log('Incorrect worker defined in heartBeat list: '.$worker, Logger::WARNING, __FILE__, __LINE__);
                //static::$redis->lRem(static::$heartBeatList, $worker, 1);
                continue;
            }

            $workerId = $tmp[0];
            $lastHeartBeat = $tmp[1];

            if(!static::checkHeartBeatTime($lastHeartBeat)) {
                Logger::log('Worker: ' . $workerId . ' heartBeat time expired.', Logger::WARNING, __FILE__, __LINE__);
                static::moveWorkerQueueTasksToMainQueue($workerId);
                static::$redis->lRem(static::$taskQueue . ':workers-heartbeat', $worker, 1);
            }
        }

    }

    /**
     * Inspect tasks queue
     *
     * @static
     * @access private
     * @return void
     */
    private static function inspectTasksQueue(): void
    {
        
        $tasksCount = static::$redis->lLen(static::$taskQueue);

        if($tasksCount > 10) {
            Logger::log('Queue count: '.$tasksCount.' > 10', Logger::WARNING, __FILE__, __LINE__);
        }

    }

    /**
     * Inspect workers queue
     *
     * @static
     * @access public
     * @return false|void
     */
    public static function inspectWorkersQueue() {

        $workers = static::getWorkersFromWorkersQueueList();

        if(empty($workers)) {
            // This can be empty, because of no new tasks.
            return false;
        }

        foreach($workers as $worker) {

            # Check heartbeat check exists
            # MQ_d876g66886gfd:worker:7099
            $tmp = explode(':', $worker);
            $workerId = $tmp[2];

            if(!static::heartBeatExists($workerId)) {
                Logger::log('Worker Queue exists but heart beat not: '.$workerId, Logger::WARNING, __FILE__, __LINE__);
                static::moveWorkerQueueTasksToMainQueue($workerId);
            }
        }
    }

    /**
     * Inspect heart beat and workers queue match
     *
     * @static
     * @access private
     * @return bool
     */
    private static function inspectHeartBeatAndWorkersQueueMatch() : bool {

        $taskQueueWorkers = static::getWorkersCountFromWorkersQueueList();
        $heartBeatWorkers = static::getWorkersCountFromHeartBeat();

        if($taskQueueWorkers != $heartBeatWorkers) {
            Logger::log('Workers amount in heartBeat : '.$heartBeatWorkers.' not matches with workers Queue list: '.$taskQueueWorkers, Logger::WARNING, __FILE__, __LINE__);
            return False;
        }

        return True;
    }

    /**
     * Check heart beat time
     *
     * @static
     * @access private
     * @param $lastHeartBeat
     * @return bool
     */
    private static function checkHeartBeatTime($lastHeartBeat) : bool {

        $lastHeartBeat = (int) $lastHeartBeat;

        if (time() - $lastHeartBeat > static::$heartBeatExpireMinutes * 60) {
            return False;
        }

        return True;
    }

    /**
     * Move worker queue tasks to main queue
     *
     * @static
     * @access private
     * @param $workerId
     * @return void
     */
    private static function moveWorkerQueueTasksToMainQueue($workerId) : void {

        $workerTasksCount = static::getWorkerTasksQueueCountFromWorkersQueueList($workerId);

        if($workerTasksCount < 1) {
            return;
        }

        Logger::log('Moving '.$workerTasksCount.' tasks of worker '.$workerId.' to main queue', Logger::INFO, __FILE__, __LINE__);

        $task = True;

        while($task) {
            $task = static::$redis->rPopLPush(static::$taskQueue . ':worker:' . $workerId, static::$taskQueue);
        }

    }

    /**
     * Check if heart beat exists
     *
     * @static
     * @access private
     * @param $workerId
     * @return bool
     */
    private static function heartBeatExists($workerId) : bool {

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

    /**
     * Return Array in Message/Queue reports
     *
     * @static
     * @access public
     * @return array
     */
    public static function getAllReports() : array {
        return [
            'getQueueCount' => static::getQueueCount(),
            'getWorkersCountFromHeartBeat' => static::getWorkersCountFromHeartBeat(),
            'getWorkersFromHeartBeat' => static::getWorkersFromHeartBeat(),
            'getWorkersCountFromWorkersQueueList' => static::getWorkersCountFromWorkersQueueList(),
            'getWorkersFromWorkersQueueList' => static::getWorkersFromWorkersQueueList(),
            'getWorkersQueueCountFromWorkersQueueList' => static::getWorkersQueueCountFromWorkersQueueList(),
            'dbSize' => static::getDbSize()
        ];
    }

}