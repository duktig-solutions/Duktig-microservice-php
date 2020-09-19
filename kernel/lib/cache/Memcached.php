<?php
/**
 * Memcached Class library
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace Lib\Cache;

class Memcached {

	/**
	 * Default Configuration
	 *
	 * @access private
	 * @var array
	 */
	private $config = [
		'connections' => [
			[
				'host' => 'localhost',
				'port' => 11211,
			]
		],
		'expiration_seconds' => 300 // 5 minute
	];

	/**
	 * Memcached object
	 *
	 * @access private
	 * @var object
	 */
	private $memcached;

	/**
	 * Memcached Class constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {

		$this->config = array_merge($this->config, $config);

		$this->memcached = new \Memcached();

		# The Server can be more than one, so trying to add all
		foreach ($this->config['connections'] as $conn) {
			$this->memcached->addServer($conn['host'], (int) $conn['port']);
		}

	}

	/**
	 * Set cache content
	 *
	 * @param $key
	 * @param $data
	 * @return bool
	 */
	public function set($key, $data) {
		return $this->memcached->set($key, $data, $this->config['expiration_seconds']);
	}

	/**
	 * Get cached content
	 *
	 * @param $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->memcached->get($key);
	}

}


