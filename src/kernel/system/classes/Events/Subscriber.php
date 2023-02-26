<?php
/**
 * Events Subscriber class
 * This class uses to subscribe General Events Instance based on Redis Database
 * 
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 */  

namespace System\Events;

# Use of Redis configuration
use System\Config;
use System\Logger;
use \Redis;

/**
 * Class Event
 */
class Subscriber {

    /**
     * General events Redis configuration
     * 
     * @static
     * @access private
     * @var array
     */
    private static array $eventsRedisConfig;

    /**
     * Events Redis Connection object
     * 
     * @static
     * @access private
     * @var object
     */
    private static object $eventsRedis;

    /**
     * General Events Redis Configuration name 
     * 
     * @static
     * @access private
     * @var string
     */
    private static string $eventsRedisConfigName = 'GeneralEventsRedis';

    /**
     * How many times to try reconnect
     * 
     * @static
     * @access private 
     * @var int
     */
    private static int $connectionAttempts = 10;

    /**
     * Channels to subscribe
     * 
     * @static
     * @access private
     * @var array
     */
    private static array $subscribeToChannels = ['main'];
    
    /**
     * Connect to Redis instance
     * 
     * @access private
     * @return bool
     */
    private static function connect() : bool {

        ini_set("default_socket_timeout", '-1');

        $step = 1;
        $connected = false;

        try {

            # Initialize General Events Configuration and connection for Redis
            static::$eventsRedisConfig = Config::get()['Redis'][static::$eventsRedisConfigName];

            if (!static::$eventsRedisConfig) {
                throw new \Exception('Cannot find redis configuration by '.static::$eventsRedisConfigName);
            }

            static::$eventsRedis = new Redis();

            while($connected == false) {
        
                try {
                    
                    static::$eventsRedis->connect(static::$eventsRedisConfig['host'], static::$eventsRedisConfig['port'], 0);
                    Logger::log('Connected to Redis ' . static::$eventsRedisConfigName . ' ('.$step.' attempts)', Logger::INFO, __FILE__, __LINE__);
                    $connected = true;
                    
                } catch(\Throwable $e) {
                    Logger::Log($e->getMessage(), Logger::ERROR, $e->getFile(), $e->getLine());
                    Logger::log('Retrying to connect Redis ' . static::$eventsRedisConfigName . ' ('.$step.' attempts)', Logger::INFO, $e->getFile(), $e->getLine());
                    $step++;
                    sleep(1);
                }

                if($step >= static::$connectionAttempts) {
                    Logger::Log('Unable to Connect Redis '.static::$eventsRedisConfigName. ' after '.$step.' attempts', Logger::ERROR, __FILE__, __LINE__);
                    return false;
                }
            }            
            
            if (static::$eventsRedisConfig['password'] != '') {
                static::$eventsRedis->auth(static::$eventsRedisConfig['password']);
            }

            static::$eventsRedis->select(static::$eventsRedisConfig['database']);
                        
            return true;

        } catch(\Throwable $e) {
            Logger::Log($e->getMessage(), Logger::ERROR, $e->getFile(), $e->getLine());
            return false;
        }

    }

    /**
     * Ping the Redis connection
     * Re-connect if not connected
     * 
     * @access private
     * @return bool
     */
    private static function pingConnection() : bool {

        if(!static::$eventsRedis) {
            return static::connect();
        }

        if(!static::$eventsRedis->ping()) {
            return static::connect();
        }

        return true;
    }

    /**
     * Subscribe to Redis Events instance
     * 
     * @access public
     * @param array $callback
     * @param array | null $subscribeToChannels
     * @param string | null $eventsRedisConfigName
     * @return bool
     */
    public static function subscribe(array $callback, ?array $subscribeToChannels = null, ?string $eventsRedisConfigName = null) : bool {
        
        if(!is_null($eventsRedisConfigName)) {
            static::$eventsRedisConfigName = $eventsRedisConfigName;
        }

        if(!is_null($subscribeToChannels)) {
            static::$subscribeToChannels = $subscribeToChannels;
        }
        
        if(empty(static::$subscribeToChannels)) {
            Logger::log('The subscription channels list is empty. Unable to subscribe!', Logger::ERROR, __FILE__, __LINE__);
            return false;
        }

        if(!static::pingConnection()) {
            return false;
        }

        # Subscribe to Events channels

        # Shit! This made me crazy!
        # Assuming, if there is no Subscriber channels, this container restarting without any error message
        # No internet connection in Home office.
        # Listening : Paul Oakenfold/2001 - Ibiza/Part-2 - 05_U2_beautiful_day_the_perfecto_mix
        try {
            Logger::log('Subscribing to Redis Events chunnels '.implode(',', static::$subscribeToChannels).'.', Logger::INFO, __FILE__, __LINE__);
            self::$eventsRedis->subscribe(static::$subscribeToChannels, $callback);
        } catch(\Throwable $e) {
            Logger::log('Unable to subscribe Redis Channel - ' . $e->getMessage(), Logger::ERROR, $e->getFile(), $e->getLine());
            return false;
        }

        Logger::log('Subscribed to Redis Events chunnels '.implode(',', static::$subscribeToChannels).' successfully.', Logger::INFO, __FILE__, __LINE__);
        return true;

    }

}