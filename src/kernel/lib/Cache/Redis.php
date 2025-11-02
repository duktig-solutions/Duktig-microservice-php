<?php
/**
 * Redis Cache Class library
 * 
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @uses PhpRedis extension: https://github.com/phpredis/phpredis
 * @version 1.0.2
 */
namespace Lib\Cache;

# PHPRedis Extension
use Redis as RedisClient;
use RedisException;

/**
 * class Redis
 */
class Redis {

	/**
	 * Default Configuration
	 *
	 * @access private
	 * @var array
	 */
	private array $config = [
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
        'read_write_timeout' => 0,
        'password' => '',
        'cache_expiration_seconds' => 300 // 5 minute		
	];

    /**
     * Redis object
     *
     * @access private
     * @requires php-redis extension
     * @var RedisClient object
     */
    private RedisClient $redis;

	/**
	 * Class constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {

        # Set configuration
        $this->config = array_merge($this->config, $config);

        # Connect to Redis
        $this->redis = new RedisClient();
        $this->redis->connect($this->config['host'], $this->config['port']);

        # Auth if password specified
        if ($this->config['password'] != '') {
            $this->redis->auth($this->config['password']);
        }

        // Select the database
        $this->redis->select($this->config['database']);

	}

    # Let's close the Redis connection
    public function __destruct() {
        $this->redis->close();
    }

    /**
     * Set cache content as String
     *
     * @access public
     * @param string $key
     * @param mixed $data
     * @param int|null $expiresInSeconds
     * @return void
     * @throws RedisException
     */
	public function set(string $key, mixed $data, ?int $expiresInSeconds = null): void
    {
        
        $this->redis->set($key, $data);

        # If the expiration time not defined, we get from Configuration.
        # Expiration in Seconds 
        if(is_null($expiresInSeconds)) {
            $expiresInSeconds = $this->config['cache_expiration_seconds'];
        }

        $this->redis->expire($key, $expiresInSeconds); 
                
	}

    /**
     * Set array value to cache.
     * WARNING! This uses Json encode and decode for array functional.
     *
     * Twitted this, when developed: https://twitter.com/DuktigS/status/1368224246325985281/photo/1
     *
     * @access public
     * @param string $key
     * @param array $data
     * @param integer|null $expiresInSeconds
     * @return void
     * @throws RedisException
     */
    public function setArray(string $key, array $data, ?int $expiresInSeconds = null): void
    {

        $data = json_encode($data, JSON_NUMERIC_CHECK);
        $this->set($key, $data, $expiresInSeconds);

    }

	/**
	 * Get cached content
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key): mixed
    {
		return $this->redis->get($key);
	}

    /**
     * Get cached content as an array
     *
     * @param string $key
     * @return mixed
     */
    public function getArray(string $key): mixed
    {

        $data = $this->get($key);

        if($data) {
            return json_decode($data, true);
        }

        return null;
    }

}


