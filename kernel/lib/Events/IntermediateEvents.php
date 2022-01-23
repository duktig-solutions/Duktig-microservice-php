<?php
/*
 * Class to send and Receive intermediate data center Events
 * 
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @uses PhpRedis extension: https://github.com/phpredis/phpredis
 * @version 1.0.0
 */ 
namespace Lib\Events;

# PHPRedis Extension
use \Redis as RedisClient;

# Get Configuration to connect to redis server
use System\Config;

# Event data structure template
use \Lib\Events\Event;

/*
 * Class IntermediateEvents
 */ 
class IntermediateEvents {

    /**
	 * Default Configuration
	 *
	 * @access private
	 * @var array
	 */
	private static $config = [
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
        'read_write_timeout' => 0,
        'password' => ''
	];

    /**
     * Redis object
     * 
     * @access protected
     * @var object
     */
    protected static $redis = Null;

    /**
     * Connect to Redis server as a token storage
     * 
     * @access protected
     * @return void
     */
    protected static function connectStorage() : void {

        # Get connaction from application config and merge with defaults
        static::$config = array_merge(static::$config, Config::get()['Redis']['GeneralEventsRedis']);
        
        # Connect to Redis
        static::$redis = new RedisClient();
        static::$redis->connect(static::$config['host'], static::$config['port'], 0);

        # Auth if password specified
        if (static::$config['password'] != '') {
            static::$redis->auth(static::$config['password']);
        }

        # Select the database
        static::$redis->select(static::$config['database']);

    }
    
    /**
     * Ping to Redis server
     * Each method of this class uses this method to be sure the connection is active.
     * This method will automatically conenct to Redis server if not connected or no ping.
     * 
     * @access public
     * @return mixed
     */
    protected static function pingStorage() {

        if(is_null(static::$redis)) {
            static::connectStorage();
            return true;
        }

        if(!static::$redis->ping()) {
            static::connectStorage();
        }

    }

    /**
     * Publish an event to Intermediate data center
     * 
     * @static
     * @access public
     * @param string $channel
     * @param string $dataJson
     * @return bool
     */
    public static function publish(string $channel = 'main', string $dataJson) : bool {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Publish an event to channel
        static::$redis->publish($channel, $dataJson);

        return true;
    }

}