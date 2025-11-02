<?php
/**
 * Class to manage User Auth storage using Redis Server
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @uses PhpRedis extension: https://github.com/phpredis/phpredis
 * @version 1.0.1
 *
 * It is now 5 Dec 2023. Listening to nice Christmas songs...
 *          Haha, it is now 31 Dec 2024 Listening to nice Christmas songs...
 *
 * Data structure of users token storage
 *
 * Each user has a HASH list of devices logged in
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
 * HASH $uid (example: ecdd2559-9e05-4af3-b4e4-f1154a32d792)
 *      KEY1: $sessionId1 (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_57ffdeb2b93236cd8d7506db08f9fe59806366bb)
 *      VALUE1: $storageData1 (example: OK)
 *      KEY2: $sessionId2 (example: J34x1iXqyqWkIh60zxwGxzWiyYHKFw_df85g7s8d6gf6d78dfg687f68g7f86d86fgfg86)
 *      VALUE2: $storageData2 (example: OK)
 *      ...
 *
 * Configuration of Tokens Storage for this functionality defined in:
 * /app/Config/app.php
 *
 * Redis -> AuthStorage
 *
 */
namespace Lib\Auth;

# PHPRedis Extension
use \Redis as RedisClient;

# Get Configuration to connect to redis server
use RedisException;
use System\Config;

/**
 * class Storage
 */
class Storage {

    /**
	 * Default Configuration
	 *
	 * @access private
	 * @var array
	 */
	private static array $config = [
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
     * @var RedisClient|null
     */
    protected static ?RedisClient $redis = Null;

    /**
     * Connect to Redis server as a token storage
     *
     * @access protected
     * @return void
     * @throws RedisException
     */
    protected static function connectStorage() : void {

        # Get connection from application Config and merge with defaults
        static::$config = array_merge(static::$config, Config::get()['Redis']['AuthStorage']);

        # Connect to Redis
        static::$redis = new RedisClient();
        static::$redis->connect(static::$config['host'], static::$config['port']);

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
     * @static
     * @access public
     * @return void
     * @throws RedisException
     */
    protected static function pingStorage() : void {

        if(is_null(static::$redis)) {
            static::connectStorage();
            return;
        }

        if(!static::$redis->ping()) {
            static::connectStorage();
        }

    }

    /**
     * In 2FA Authentication, Password reset and other times,
     * this method will be used to Set Verification data into Storage.
     *
     * The verification Data will look like this:
     *
     * uid
     * email
     * phone
     * verifyBy
     * deviceId
     * remoteIP
     * userAgent
     *
     * @param array $verificationPayload
     * @param string $verificationToken
     * @param string $verificationCode
     * @param string $prefix
     * @return void
     */
    public static function setVerificationData(array $verificationPayload, string $verificationToken, string $verificationCode, string $prefix = 'verification') : void {

        static::pingStorage();

        # Select the Verification database
        static::$redis->select(static::$config['VerificationDatabase']);

        $key = sprintf("%s-%s-%s", $prefix, $verificationToken, $verificationCode);

        static::$redis->set($key, json_encode($verificationPayload));

        $VerificationStorageExpiration = Config::get()['Auth']['VerificationStorageExpiration'];

        # The Verification data in storage can stay in specified expiration time.
        static::$redis->expire($key, (strtotime($VerificationStorageExpiration) - time()));

    }

    /**
     * Destroy verification data
     *
     * @param string $verificationToken
     * @param string $verificationCode
     * @param string $prefix
     * @return void
     * @throws RedisException
     */
    public static function destroyVerificationData(string $verificationToken, string $verificationCode, string $prefix = 'verification'): void
    {

        static::pingStorage();

        # Select the Verification database
        static::$redis->select(static::$config['VerificationDatabase']);

        $key = sprintf("%s-%s-%s", $prefix, $verificationToken, $verificationCode);

        static::$redis->del($key);

    }

    /**
     * Get verification data
     *
     * @param string $verificationToken
     * @param string $verificationCode
     * @param string $prefix
     * @return bool|array
     */
    public static function getVerificationData(string $verificationToken, string $verificationCode, string $prefix = 'verification') : bool|array {

        static::pingStorage();

        # Select the 2FA verification database
        static::$redis->select(static::$config['VerificationDatabase']);

        $key = sprintf("%s-%s-%s", $prefix, $verificationToken, $verificationCode);

        $content = static::$redis->get($key);

        if(empty($content)) {
            return false;
        }

        return json_decode($content, true);
    }

    /**
     * Set a token to storage by uid
     *
     * @access public
     * @param string $uid
     * @param string $deviceId User signed in device id
     * @param array $storageData Session payload
     * @param string $KeyExpires Expiration time of KEY as string. i.e. +1 month
     * @param string $jti Jti used to create a session key
     * @return bool
     * @throws RedisException
     */
    public static function setAuthData(string $uid, string $deviceId, array $storageData, string $KeyExpires, string $jti) : bool {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['TokenStorageDatabase']);

        # Define session Id
        $storageSessionId = $jti;

        static::$redis->hMSet($storageSessionId, $storageData);
        static::$redis->expire($storageSessionId, (strtotime($KeyExpires) - time()));

        # Now set this to a User Sessions Collection as a HASH
        $storageCollectionId = 'ss-'.$uid;
        static::$redis->hSet($storageCollectionId, $storageSessionId, 'OK');

        # HASH list expiration
        # This wil always expire at last session creation time.
        static::$redis->expire($storageCollectionId, (strtotime($KeyExpires) - time()));

        return True;
    }

    /**
     * Refresh Sessions list.
     * Remove expired sessions from the list
     * Listening: Dj Dado - Give me Love - Antique Radio Cut
     *
     *
     * @param string $uid
     * @return array
     * @throws RedisException
     */
    public static function getSessionList(string $uid) : array {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['TokenStorageDatabase']);

        $storageCollectionId = 'ss-'.$uid;

        $sessionList = static::$redis->hGetAll($storageCollectionId);

        if(!$sessionList) {
            return [];
        }

        foreach($sessionList as $storageSessionId => $ok) {

            $storageData = static::$redis->hGetAll($storageSessionId);

            if(empty($storageData)) {
                static::$redis->hDel($storageCollectionId, $storageSessionId);
            }

        }

        if(empty($sessionList)) {
            static::$redis->del($storageCollectionId);
        }

        return array_keys($sessionList);
    }

    /**
     * Get All login sessions detailed
     *
     * @param string $uid
     * @return array
     * @throws RedisException
     */
    public static function getSessions(string $uid) : array {

        $sessionIds = static::getSessionList($uid);

        if(!$sessionIds) {
            return [];
        }

        $sessions = [];

        foreach($sessionIds as $sessionId) {
            $storageData = static::$redis->hGetAll($sessionId);
            $storageData['session_id'] = $sessionId;
            $sessions[] = $storageData;
        }

        return $sessions;

    }

    /**
     * Get And return user session data by id
     *
     * @param string $jti
     * @return array
     * @throws RedisException
     */
    public static function getSessionByJti(string $jti) : array {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['TokenStorageDatabase']);

        # Define session Id
        $storageSessionId = $jti;

        $storageData = static::$redis->hGetAll($storageSessionId);

        if(!$storageData) {
            return [];
        }

        $storageData['date_last_access'] = date('Y-m-d H:i:s');

        static::$redis->hMSet($storageSessionId, $storageData);

        return $storageData;

    }

    /**
     * Get Role Service Permissions
     *
     * @return array|RedisClient
     * @throws RedisException
     */
    public static function getAllRoles(): array|RedisClient
    {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['RolesPermissionsDatabase']);

        $storageData = static::$redis->hGetAll('roles');

        if(!$storageData) {
            return [];
        }

        return $storageData;

    }

    /**
     * Set All Roles
     *
     * @param array $roles
     * @return void
     */
    public static function setAllRoles(array $roles): void
    {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['RolesPermissionsDatabase']);

        static::$redis->hMSet('roles', $roles);

        # HASH list expiration
        # This wil always expire at last session creation time.
        static::$redis->expire('roles', (strtotime('+5 minutes') - time()));

    }

    /**
     * Delete all user sessions by uid and return deleted items count
     *
     * @access public
     * @param string $uid
     * @return int
     * @throws RedisException
     */
    public static function deleteAllSessions(string $uid) : int {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['TokenStorageDatabase']);

        # Trying to get all from HASH list by uid
        $items = self::$redis->hGetAll('ss-'.$uid);

        # There are no data
        if(!$items) {
            return 0;
        }

        # Delete KEYs as stored with expiration
        foreach($items as $sessionId => $itemValue) {
            self::$redis->unlink($sessionId);
        }

        # Delete this HASH
        self::$redis->unlink('ss-'.$uid);

        return count($items);

    }

    /**
     * Get Session by Id
     *
     * @param string $sessionId
     * @return false|array|RedisClient
     */
    public static function getSessionById(string $sessionId): false|array|RedisClient
    {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['TokenStorageDatabase']);

        return self::$redis->hGetAll($sessionId);

    }

    /**
     * Delete session by Id
     *
     * @param string $uid
     * @param string $sessionId
     * @return void
     */
    public static function deleteSessionById(string $uid, string $sessionId): void {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['TokenStorageDatabase']);

        $storageCollectionId = 'ss-'.$uid;

        $sessionList = static::$redis->hGetAll($storageCollectionId);

        # Delete session Id from sessions list
        if(!empty($sessionList)) {

            foreach($sessionList as $storageSessionId => $ok) {

                if($sessionId == $storageSessionId) {
                    static::$redis->hDel($storageCollectionId, $storageSessionId);
                }

            }

        }

        if(empty($sessionList)) {
            static::$redis->del($storageCollectionId);
        }

        # Delete Session data
        static::$redis->del($sessionId);

    }

    /**
     * Clean up Sign in attempts by identifier.
     *
     * @param string $identifier
     * @return void
     */
    public static function cleanupSignInAttempts(string $identifier) : void {

        $storageKey = 'attempts-'.$identifier;

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['SignInAttemptsDatabase']);

        static::$redis->del($storageKey);
    }

    /**
     * Add (increment) Sign in attempts
     *
     * @param string $identifier
     * @param string $expiration
     * @return void
     */
    public static function addSignInAttempts(string $identifier, string $expiration): void
    {

        $storageKey = 'attempts-'.$identifier;

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['SignInAttemptsDatabase']);

        $attempts = static::$redis->hGetAll($storageKey);

        if(!$attempts) {
            $attempts = [];
        }

        $attempts[] = date('Y-m-d H:i:s');

        static::$redis->hMSet($storageKey, $attempts);

        # HASH list expiration
        # This wil always expire at last session creation time.
        static::$redis->expire($storageKey, (strtotime($expiration) - time()));

    }

    /**
     * Check if it's reached the sign in attempts
     *
     * @param string $identifier
     * @param int $allowedAttempts
     * @return bool
     */
    public static function isSignInAttemptsReached(string $identifier, int $allowedAttempts) : bool {

        $storageKey = 'attempts-'.$identifier;

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Select the Auth Storage Database
        static::$redis->select(static::$config['SignInAttemptsDatabase']);

        $attempts = static::$redis->hGetAll($storageKey);

        if(is_array($attempts) and count($attempts) >= $allowedAttempts) {
            return true;
        }

        return false;
    }

    // ================================== deprecated functionality ============================ //

    /**
     * Update session data in storage
     *
     * @access public
     * @param string $uid
     * @param string $sessionId
     * @param array $data
     * @return bool
     * @todo figure out this
     */
    public static function updateSession(string $uid, string $sessionId, array $data) : bool {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Get Session Data
        $sessionData = static::$redis->hGetAll('sess_'.$sessionId);

        if(!$sessionData) {

            # This value removed or expired,
            # Lets anyway try to delete from user HASH
            self::$redis->hDel($uid.'_sessions', $sessionId);

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
     * @param string $uid
     * @param array $data
     * @return bool
     * @throws RedisException
     * @todo figure out this method
     */
    public static function updateLogin(string $uid, array $data) : bool {

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
        $accountData = self::$redis->hGetAll($uid.'_login');

        if(!$accountData) {
            return False;
        }

        # Set login Data
        # With this data we're always keeping fresh the user roleId and status
        # When user access to server, the token verification functionality
        # should look for user roleId and status right from here.
        static::$redis->hMSet(
            $uid.'_login',
            array_merge($accountData, $data)
        );

        return true;
    }

    /**
     * Get token value by uid and sessionId
     *
     * @access public
     * @param string $uid
     * @param string $sessionId
     * @return mixed
     */
    public static function get(string $uid, string $sessionId): mixed
    {

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
            self::$redis->hDel($uid.'_sessions', $sessionId);

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
        $loginData = self::$redis->hGetAll($uid.'_login');

        # If we have no data about login, assuming something was damaged. Let's remove this session data
        if(!$loginData) {
            self::$redis->hDel($uid.'_sessions', $sessionId);
            self::$redis->del('sess_'.$sessionId);
        }

        # Now, if all is correct let's return combined array
        return array_merge($sessionData, $loginData, ['uid' => $uid, 'sessionId' => $sessionId]);

    }

    /**
     * Delete specified token by uid and sessionId
     *
     * @access public
     * @param string $uid
     * @param string $sessionId
     * @return void
     *
     * @deprecated
     */
    public static function delete(string $uid, string $sessionId): void
    {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Delete from KEY
        self::$redis->unlink('sess_'.$sessionId);

        # Delete from HASH
        self::$redis->hDel($uid.'_sessions', $sessionId);

    }

    /**
     * Delete User login Data from storage
     *
     * @param string $uid
     * @return void
     * @deprecated
     */
    public static function deleteLogin(string $uid): void
    {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Delete this HASH
        self::$redis->unlink($uid.'_login');

    }

    /**
     * Cleanup User HASH list in case if some of the KEY items expired
     *
     * @access public
     * @param string $uid
     * @return int
     * @throws RedisException
     */
    public static function cleanupHASH(string $uid) : int {

        # Try to ping/reconnect to Redis server
        static::pingStorage();

        # Trying to get all from HASH list by user Id
        $items = self::$redis->hGetAll($uid);

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
                self::$redis->hDel($uid, $sessionId);
                $deletedCount++;
            }

        }

        return $deletedCount;

    }

}