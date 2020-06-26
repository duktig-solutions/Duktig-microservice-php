<?php
/**
 * Application Main Configuration file.
 * See structure and explanation bellow.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
return [
    # Your project/setup name
    'ProjectName' => 'Duktig.Microservice',

    # Your project version
    'ProjectVersion' => '1.0.0',

	# System Id for each instance of Duktig.Microservice
	'SystemId' => 'Dev',

    # Log errors. All type of error logs located in: /app/log
    'LogErrors' => 1,

    # Display errors configuration flag for php.ini
    'DisplayErrors' => 1,

    # Default date time zone for application
    'DateTimezone' => 'Asia/Yerevan', // 'America/Los_Angeles',

    # Application mode can be: production | development and others
    # If it is production mode, detailed error messages will not be displayed in Response json.
    'Mode' => 'development',

    # If application is under maintenance any Request will be
    # Responded with 503 Status (Service not available).
    'UnderMaintenance' => 0,

    # Enable or disable CLI mode.
    # In some cases you can temporary disable CLI functionality.
    # Set this to "0" to stop Cli route parsing and functionality.
    'DisableCLI' => 0,

    # Database Connection Configuration
    # Each model in /app/models is able to use/start with one connection section.
    'Databases' => [

	    # Section name defined by Developer will be used in model file.
	    'ExampleDatabaseConnectionInstance' => [

		    # Database Driver Class can be defined as MySQLi
		    'driver' => 'MySQLi',
		    # Host
		    'host' => 'localhost',
		    # Port
		    'port' => 3306,
		    # MySQL Username
		    'username' => 'root',
		    # MySQL Password
		    'password' => 'abc123',
		    # MySQL Database name
		    'database' => 'Duktig',
		    # Charset
		    'charset' => 'utf8'
	    ],

	    # Authorization Database
	    'Auth' => [
		    'driver' => 'MySQLi',
		    'host' => 'localhost',
		    'port' => 3306,
		    'username' => 'root',
		    'password' => 'abc123',
		    'database' => 'DuktigAuth',
		    'charset' => 'utf8'
	    ],

	    # Authorization for Database backup user
	    'BackupConn' => [
		    'driver' => 'MySQLi',
		    'host' => 'localhost',
		    'port' => 3306,
		    'username' => 'root',
		    'password' => 'abc123',
		    'database' => '2do',
		    'charset' => 'utf8'
	    ]
    ],

    # Authentication by key
    'Auth' => [

        # The key name in headers
        'AuthKey' => 'X-Auth-Key',

        # The key value in headers
        'AuthKeyValue' => 'aFrt$63^_tgrDlp-0Ar20-06G'
    ],

	# Authentication by key for Developers
	'AuthDevelopers' => [

		# The key name in headers
		'DevAuthKey' => 'X-Dev-Auth-Key',

		# The key value in headers
		'DevAuthKeyValue' => 'FRE-20%Dev-Gro@25464-'

	],

    # JWT Authorization Configuration
    'JWT' => [

    	'access_token' => [

    		# Issuer
		    'iss' => 'Duktig.dev.iss',

		    # Audience (The area where this token allowed)
		    # In the future this can be used as User access area definition.
		    # i.e. admin|manager|data or data (only)
		    'aud' => 'Duktig.dev.general.aud',

		    # The subject of JWT
		    'sub' => 'Duktig.dev.general.sub',

		    # JWT ID. Case sensitive unique identifier of the token even among different issuers.
		    'jti' => 'Duktig.dev.general.jti',

		    # Access token Expiration time
		    # Warning! This string will be used in method strtotime(exp)
		    'exp' => '+1 day',

		    # Refresh token Expiration time
		    # Warning! This string will be used in method strtotime(exp)
		    'refresh_token_exp' => '+1 month',

		    # Token can start after given time
		    # Warning! This string will be used in method strtotime(exp)
		    'nbf' => '+0 minutes',

		    # 256-bit-secret key
		    'secretKey' => '_tSe7#K209wG@g1vroW~43985&c~edra',
	    ],

	    'refresh_token' => [

		    # Issuer
		    'iss' => 'Duktig.dev.iss',

		    # Audience (The area where this token allowed)
		    # In the future this can be used as User access area definition.
		    # i.e. admin|manager|data or data (only)
		    'aud' => 'Duktig.dev.auth.aud',

		    # The subject of JWT
		    'sub' => 'Duktig.dev.auth.sub',

		    # JWT ID. Case sensitive unique identifier of the token even among different issuers.
		    'jti' => 'Duktig.dev.auth.jti',

		    # Refresh token Expiration time
		    # Warning! This string will be used in method strtotime(exp)
		    'exp' => '+1 month',

		    # Token can start after given time
		    # Warning! This string will be used in method strtotime(exp)
		    'nbf' => '+0 minutes',

		    # 256-bit-secret key
		    'secretKey' => '$563ty7G4X8#9(j1@3-=',

		    # Special key to verify user account payload
		    'account_key' => '!D#_REYI-CRO-2323cd&Fro@Gdf98fg8d97'
	    ]

    ],

	# Memcached - Caching configuration.
	# This can be used by caching middleware for API request Data.
	'ResponseDataCaching' => [

		# Servers can be more than one.
		'connections' => [
			[
				'host' => 'localhost',
				'port' => 11211,
			]
		],

		# Cached Data expiration time in seconds.
		'expiration_seconds' => 300 # 5 minute
	],

	# System binary executables
	'Executables' => [
		'curl' => '/usr/bin/curl',
		'mysqldump' => '/usr/local/mysql/bin/mysqldump'
	],

	# Backup Configuration
	'Backups' => [
		# Databases to backup
		'Databases' => [
			[
				'database' => 'Duktig',
				'excluded_tables' => []
			],
			[
				'database' => 'DuktigAuth',
				'excluded_tables' => []
			]
		],

		# If this value is empty, the system will automatically backup into /backups/db/
		# Notice: The last slash is important if you specify a path.
		'BackupDatabasesDir' => '',

		# How many copies will keep the backup.
		# for instance, setting number 7 will assume the backup will remove oldest one after 7 copies.
		'DatabasesBackupSteps' => 7
	]

];
