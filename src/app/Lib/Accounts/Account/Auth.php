<?php
/**
 * Auth Access/Refresh tokens generator class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.1.0
 * 
 * @todo in this I'm using lib\auth\jwt::blabla ... but I'm extending it here... maybe static static::...
 */
namespace App\Lib\Accounts\Account;

use Exception;
use \System\Config;
use \Lib\HTTP\ClientInfo;
use System\HTTP\Request;

class Auth extends \Lib\Auth\Jwt {

    /**
     * Generate Tokens and register user data in Token Storage
     *
     * @static
     * @access public
     * @param array $account
	 * @param Request $request
     * @return array
     * @throws Exception
     */
    public static function Authorize(array $account, Request $request) : array {
		
		# Assuming this can be an IP Address provided by Nginx Proxy pass
		if(!empty($request->headers('X-Real-Ip'))) {
            $userIpAddress = $request->headers('X-Real-Ip');
        } else {
            $userIpAddress = \Lib\HTTP\ClientInfo::ipAddress();
        }

		$deviceId = $request->headers('X-Device-Id');

		# Define TokenStorage data
		$tokenStorageKey = $account['userId'].'_'.sha1($deviceId);
		$tokenStorageData = [
            'userId' => $account['userId'],
            'roleId' => $account['roleId'],
            'status' => $account['status'],
            'displayName' => $account['displayName'],
            'firstName' => $account['firstName'],
            'lastName' => $account['lastName'],
            'dateLogin' => date('Y-m-d H:i:s'),
            'loginIp' => $userIpAddress,
            'userAgent' => ClientInfo::userAgent(),
            'deviceId' => $deviceId,
            'tokenExpires' => date('Y-m-d H:i:s', strtotime(Config::get()['JWT']['access_token']['exp'])),
            'refreshTokenExpires' => date('Y-m-d H:i:s', strtotime(Config::get()['JWT']['refresh_token']['exp']))
        ];
        
        \Lib\Auth\Storage::setAuthData(
            $account['userId'],
            $tokenStorageKey, 
            $tokenStorageData, 

            # Set the expiration time on KEY for Login Key.
            # This will destroy in right time.
            Config::get()['JWT']['access_token']['exp'],
            # Set the expiration time for HASH
            # This can alive as refresh token expiration time
            Config::get()['JWT']['refresh_token']['exp']

            # !!! As you see, the HASH lifetime is longer than KEY lifetime.
            # KEYs can be destroyed early, as getting expired,
            # but HASH lifetime is longer until last added KEY time
        );
		
        return [
        	'access_token' => static::AccessToken($account, $deviceId),
	        'expires_in' => static::AccessTokenExpiresIn(),
			'refresh_token' => static::RefreshToken($account, $deviceId)
        ];

    }

	/**
	 * Return Access token Expiration time from config
	 *
	 * @static
	 * @access public
	 * @return int
	 */
    public static function AccessTokenExpiresIn() : int {

	    $config = Config::get()['JWT']['access_token'];

	    return strtotime($config['exp']);
    }

	/**
	 * Generate access token
	 *
	 * @static
	 * @access public
	 * @param array $account
	 * @param string $deviceId
	 * @return string
	 * @throws Exception
	 */
	public static function AccessToken(array $account, string $deviceId) : string {

    	$config = Config::get()['JWT']['access_token'];
		
		return \Lib\Auth\Jwt::encode(
			[
				# Issuer
				'iss' => $config['iss'],

				# Audience (Identifies the recipients that the JWT is intended for)
				'aud' => $config['aud'],

				# The subject of JWT
				'sub' => $config['sub'],

				# JWT ID - each token should have its own Id by user and device id.
				# Each token related to user but also to device. 
				'jti' => \Lib\Auth\Password::encrypt($account['userId'].'__'.$config['payload_secure_encryption_key'] . $deviceId),

				# Not before (allow login not before given time)
				'nbf' => strtotime($config['nbf']),

				# Issued at
				'iat' => time(),

				# Token expiration time
				'exp' => strtotime($config['exp']),

				# Additional payload data, in this case user account Id, deviceId
				'account' => [
					'userId' => $account['userId'],
					'deviceId' => $deviceId
				]
			],
			$config['secretKey']
		);

	}

	/**
	 * Generate refresh token
	 *
	 * @param array $account
	 * @param string $deviceId
	 * @return string
	 * @throws Exception
	 */
    public static function RefreshToken(array $account, string $deviceId) : string {

	    $config = Config::get()['JWT']['refresh_token'];
		
	    return \Lib\Auth\Jwt::encode(
		    [
			    # Issuer
			    'iss' => $config['iss'],

			    # Audience (Identifies the recipients that the JWT is intended for)
			    'aud' => $config['aud'],

			    # The subject of JWT
			    'sub' => $config['sub'],

			    # JWT ID
			    'jti' => \Lib\Auth\Password::encrypt($account['userId'] . $config['payload_secure_encryption_key'] . '__' . $deviceId),

			    # Not before (allow login not before given time)
			    'nbf' => strtotime($config['nbf']),

			    # Issued at
			    'iat' => time(),

			    # Refresh token expiration time
			    'exp' => strtotime($config['exp']),

			    # Additional payload data, in this case user account Id, deviceId and special key
			    'account' => [
					'userId' => $account['userId'],
					'deviceId' => $deviceId,
					'secureKey' => \Lib\Auth\Password::encrypt($account['userId'].$config['payload_secure_encryption_key'].$deviceId)
			    ]
		    ],
		    $config['secretKey']
	    );

    }

}