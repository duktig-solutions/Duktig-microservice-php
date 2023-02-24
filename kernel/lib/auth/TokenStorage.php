<?php
/**
 * Class to manage token Storage on Redis Server
 *  
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @uses PhpRedis extension: https://github.com/phpredis/phpredis
 * @version 1.1.0
 * 
 * Data structure of users token storage
 * 
 * Each user have a HASH list of devices logged in 
 * and each device token as separate KEY value with expiration time.
 * 
 * Example of each Device Login token
 * 
 * KEY: $sessionId1 (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_57ffdeb2b93236cd8d7506db08f9fe59806366bb)
 * VALUE: $storageData1 - Json encoded information about user session containing deviceId
 * KEY: $sessionId2 (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_df85g7s8d6gf6d78dfg687f68g7f86d86fgfg86)
 * VALUE: $storageData2 - Json encoded information about user session containing deviceId
 * 
 * and the HASH list of sessions
 * 
 * HASH $userId (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw)
 *      KEY1: $sessionId1 (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_57ffdeb2b93236cd8d7506db08f9fe59806366bb)
 *      VALUE1: $storageData1 (example: OK)
 *      KEY2: $sessionId2 (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_df85g7s8d6gf6d78dfg687f68g7f86d86fgfg86)
 *      VALUE2: $storageData2 (example: OK)
 *      ...
 * 
 * Configuration of Tokens Storage for this functionality defined in:
 * /app/config/app.php
 * 
 * Redis -> IntermediateDataCenterAuth
 * 
 */
namespace Lib\Auth;

# PHPRedis Extension
use \Redis as RedisClient;

# Get Configuration to connect to redis server
use System\Config;
use System\Logger;

/**
 * class TokenStorage
 */
class TokenStorage {

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

        # Get connection from application config and merge with defaults
        static::$config = array_merge(static::$config, Config::get()['Redis']['TokenStorage']);

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
     * This method will automatically connect to Redis server if not connected or no ping.
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
     * Validate User item with userId
     * To avoid returning or deleting an item(s) not relevant to given userId, 
     * we have to validate the sessionId with userId.
     * 
     * @access public
     * @param string $userId (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw)
     * @param string $sessionId (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_57ffdeb2b93236cd8d7506db08f9fe59806366bb)
     * @return bool
     */
    public static function validateUserItem(string $userId, string $sessionId) : bool {

        # Grab the userId from first part of the sessionId 
        $tmp = explode('_', $sessionId);
        
        if(count($tmp) < 2) {
            return false;
        }

        # Validation not passed if userId not equal to userId grabbed from sessionId
        if($userId != $tmp[0]) {
            return false;
        }

        return True;
    }

    /**
     * Set a token to storage by userId
     * 
     * @access public
     * @param string $userId
     * @param string $sessionId (Key to store a token)
     * @param array $storageData (values to set in storage)
     * @param string $KEYexpires Expiration time of KEY as string. i.e. +1 month
     * @param string $HASHexpires Expiration time of HASH list as string. i.e. +1 month
     * @return bool
     */
    public static function set(string $userId, string $sessionId, array $storageData, string $KEYexpires, string $HASHexpires) : bool {

        # Validate a user data if the given sessionId is relevant to userId 
        if(!static::validateUserItem($userId, $sessionId)) {
            return False;
        }

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Add data as a key->value pair and set expiration time
        # @todo review this moment 
        static::$redis->hMSet('sess_'.$sessionId, [
            'dateLogin' => $storageData['dateLogin'],
            'loginIp' => $storageData['loginIp'],
            'deviceId' => $storageData['deviceId'],
            'userAgent' => $storageData['userAgent']
        ]);

        static::$redis->expire('sess_'.$sessionId, (strtotime($KEYexpires) - time())); 

        # Now set this to a User Collection as a HASH
        static::$redis->hSet($userId.'_sessions', $sessionId, 'OK');

        # HASH list expiration
        # Assuming that the last set() call comes with newest expiration date.
        # for example, if we previously set it in 1 hour ago and this will expire with the KEY in same time,
        # Now, if we chage the expiration time and set to new one, we delaying the expiration of HASH 
        # but anyway staying with future expiration.
        static::$redis->expire($userId.'_sessions', (strtotime($HASHexpires) - time()));

        # Set login Data
        # With this data we always keeping fresh the user roleId and status
        # When user access to server, the token verification functionality 
        # should look for user roleId andstatus right from here.
        static::$redis->hMSet($userId.'_login', [
            'status' => $storageData['status'],
            'roleId' => $storageData['roleId'],
            'lastDateLogin' => $storageData['dateLogin'],
        ]);
        
        # The login data can stay as long as the session exists
        # Each time when session goes to update/create this will created or updated.
        static::$redis->expire($userId.'_login', (strtotime($KEYexpires) - time()));

        return True;
    }

    /**
     * Update session data in storage
     *
     * @access public
     * @param string $userId
     * @param string $sessionId
     * @param array $data
     * @return bool
     */
    public static function updateSession(string $userId, string $sessionId, array $data) : bool {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Validate a user data if the given sessionId is relevant to userId
        if(!static::validateUserItem($userId, $sessionId)) {
            return false;
        }

        # Get Session Data
        $sessionData = static::$redis->hGetAll('sess_'.$sessionId);

        if(!$sessionData) {

            # This value removed or expired,
            # Lets anyway try to delete from user HASH
            self::$redis->hDel($userId.'_sessions', $sessionId);

            return false;
        }

        $sessionData = array_merge($sessionData, $data);

        # Add data as a key->value pair and set expiration time
        static::$redis->hMSet('sess_'.$sessionId, $sessionData);

        return true;
    }

    /**
     * Update login data in storage
     *
     * @access public
     * @param string $userId
     * @param array $data
     * @return bool
     */
    public static function updateLogin(string $userId, array $data) : bool {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Now try to get Login info
        # This is required information for Token verification
        # And user authorization have to use this data for future steps
        # This will look like this
        /*
        Array
        (
            [status] => 1
            [roleId] => a45rzo01f3
            [lastDateLogin] => 2021-06-19 01:02
        )
        */
        $accontData = self::$redis->hGetAll($userId.'_login');

        if(!$accontData) {
            return False;
        }

        # Set login Data
        # With this data we always keeping fresh the user roleId and status
        # When user access to server, the token verification functionality
        # should look for user roleId and status right from here.
        static::$redis->hMSet(
            $userId.'_login',
            array_merge($accontData, $data)
        );

        return true;
    }

    /**
     * Get token value by userId and sessionId
     * 
     * @access public
     * @param string $userId
     * @param string $sessionId
     * @return mixed
     */
    public static function get(string $userId, string $sessionId) {

        # Validate a user data if the given sessionId is relevant to userId  
        if(!static::validateUserItem($userId, $sessionId)) {
            return Null;
        }

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # First, try to get by KEY
        # This will look like this
        /*
        Array
        (
            [dateLogin] => 2021-03-14 15:14:42
            [loginIp] => 192.168.2.152
            [deviceId] => ZenTestSessionsRoleTest
            [userAgent] => PostmanRuntime/7.26.8
        )
        */
        
        $sessionData = static::$redis->hGetAll('sess_'.$sessionId);
        
        if(!$sessionData) {
            
            # This value removed or expired,
            # Lets anyway try to delete from user HASH
            self::$redis->hDel($userId.'_sessions', $sessionId);
            
            return Null;
        }

        # Now try to get login info
        # This is required information for Token verification
        # And user authorization have to use this data for future steps
        # This will look like this
        /*
        Array
        (
            [status] => 1
            [roleId] => a45rzo01f3
            [lastDateLogin] => 2021-06-19 01:02
        )
        */
        $loginData = self::$redis->hGetAll($userId.'_login');

        # If we have not data about login, assuming something was damaged. Let's remove this session data
        if(!$loginData) {
            self::$redis->hDel($userId.'_sessions', $sessionId);
            self::$redis->del('sess_'.$sessionId);
        }

        # Now, if all is correct let's return combined array
        return array_merge($sessionData, $loginData, ['userId' => $userId, 'sessionId' => $sessionId]);
        
    }

    /**
     * Get all tokens of specified user
     * 
     * @access public
     * @param string $userId
     * @return array
     */
    public static function getAll(string $userId) : array {
        
        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Now trying to get all from HASH list by userId
        $sessions = self::$redis->hGetAll($userId.'_sessions');
        
        $result = [];

        # There are no data
        if(!$sessions) {
            return $result;
        }

        # Now, iterate trough all items and check, if some of them not exists or expired
        foreach($sessions as $sessionId => $itemValue) {
            
            # Check, if item not exists as KEY, then remove
            $redisSessionId = 'sess_'.$sessionId;
            
            $storageData = static::$redis->hGetAll($redisSessionId);

            if(!$storageData) {
            
                # This value removed or expired as KEY,
                # So we have to remove it from HASH
                self::$redis->hDel($userId, $redisSessionId);

            } else {

                # Value exists. Put it into result
                $result[$sessionId] = $storageData;//json_decode($storageData, true);
            }

        }

        return $result;
        
    }

    /**
     * Delete specified token by userId and sessionId
     * 
     * @access public
     * @param string $userId
     * @param string $sessionId
     * @return void
     */
    public static function delete(string $userId, string $sessionId) {
        
        # Validate a user data if the given sessionId is relevant to userId 
        if(!static::validateUserItem($userId, $sessionId)) {
            return Null;
        }

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Delete from KEY
        self::$redis->unlink('sess_'.$sessionId);

        # Delete from HASH        
        self::$redis->hDel($userId.'_sessions', $sessionId);

    }

    /**
     * Delete User login Data from storage
     */
    public static function deleteLogin(string $userId) {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Delete this HASH
        self::$redis->unlink($userId.'_login');

    }

    /**
     * Delete all user sessions by userId and return deleted items count
     * 
     * @access public
     * @param string $userId
     * @return int
     */
    public static function deleteAllSessions(string $userId) : int {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Trying to get all from HASH list by userId
        $items = self::$redis->hGetAll($userId.'_sessions');

        # There are no data
        if(!$items) {
            return 0;
        }

        # Delete KEYs as stored with expiration
        foreach($items as $sessionId => $itemValue) {
            self::$redis->unlink('sess_'.$sessionId);
        }

        # Delete this HASH
        self::$redis->unlink($userId.'_sessions');

        # Delete login info
        self::$redis->unlink($userId.'_login');

        return count($items);

    }

    /**
     * Cleanup User HASH list in case if some of KEY items expired
     * 
     * @access public
     * @param string $userId
     * @return int
     */
    public static function cleanupHASH(string $userId) : int {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Trying to get all from HASH list by userId
        $items = self::$redis->hGetAll($userId);

        $deletedCount = 0;

        # There are no data
        if(!$items) {
            return $deletedCount;
        }

        # Loop trough each item to ckeck existence
        foreach($items as $sessionId => $itemValue) {
            
            # Try to get Item as KEY
            $storageData = self::$redis->get($sessionId);
            
            # Item not exists as a KEY
            # We have to remove it
            if(!$storageData) {
                self::$redis->hDel($userId, $sessionId);
                $deletedCount++;
            }
            
        }

        return $deletedCount;
        
    }

}