<?php
namespace App\Lib\Auth;

class Jwt extends \Lib\Auth\Jwt {

    /**
     * Generate All tokens
     *
     * @static
     * @access public
     * @param array $account
     * @return array
     * @throws \Exception
     */
    public static function generate(array $account) : array {

        return [
        	'access_token' => static::AccessToken($account),
	        'expires_in' => static::AccessTokenExpiresIn(),
			'refresh_token' => static::RefreshToken($account)
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

	    $config = \System\Config::get()['JWT']['access_token'];

	    return strtotime($config['exp']);
    }

	/**
	 * Generate access token
	 *
	 * @static
	 * @access public
	 * @param array $account
	 * @return string
	 * @throws \Exception
	 */
	public static function AccessToken(array $account) : string {

    	$config = \System\Config::get()['JWT']['access_token'];

		return \Lib\Auth\Jwt::encode(
			[
				# Issuer
				'iss' => $config['iss'],

				# Audience (Identifies the recipients that the JWT is intended for)
				'aud' => $config['aud'],

				# The subject of JWT
				'sub' => $config['sub'],

				# JWT ID
				'jti' => $config['jti'],

				# Not before (allow login not before given time)
				'nbf' => strtotime($config['nbf']),

				# Issued at
				'iat' => time(),

				# Token expiration time
				'exp' => strtotime($config['exp']),

				# Additional payload data, in this case user account shared details
				'account' => [
					'userId' => $account['userId'],
					'firstName' => $account['firstName'],
					'lastName' => $account['lastName'],
					'email' => $account['email'],
					'roleId' => $account['roleId']
				]
			],
			$config['secretKey']
		);

	}

	/**
	 * Generate refresh token
	 *
	 * @param array $account
	 * @return string
	 * @throws \Exception
	 */
    public static function RefreshToken(array $account) : string {

	    $config = \System\Config::get()['JWT']['refresh_token'];

	    return \Lib\Auth\Jwt::encode(
		    [
			    # Issuer
			    'iss' => $config['iss'],

			    # Audience (Identifies the recipients that the JWT is intended for)
			    'aud' => $config['aud'],

			    # The subject of JWT
			    'sub' => $config['sub'],

			    # JWT ID
			    'jti' => $config['jti'],

			    # Not before (allow login not before given time)
			    'nbf' => strtotime($config['nbf']),

			    # Issued at
			    'iat' => time(),

			    # Refresh token expiration time
			    'exp' => strtotime($config['exp']),

			    # Additional payload data, in this case user account Id, email and special key
			    'account' => [
				    'userId' => $account['userId'],
				    'email' => $account['email'],
				    'account_key' => \Lib\Auth\Password::encrypt($account['userId'] . $config['account_key'] . $account['email'])
			    ]
		    ],
		    $config['secretKey']
	    );

    }

}