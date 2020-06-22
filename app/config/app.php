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

    	# DataReception Database
	    'dataReception' => [
		    'driver' => 'MySQLi',
		    'host' => 'localhost',
		    'port' => 3306,
		    'username' => 'root',
		    'password' => 'abc123',
		    'database' => 'DuktigDataReception',
		    'charset' => 'utf8'
	    ],

	    # TMP
        'DefaultConnection' => [
            'driver' => 'MySQLi',
            'host' => 'localhost',
            'port' => 3306,
            'username' => 'root',
            'password' => 'abc123',
            'database' => 'Duktig',
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
        'AuthKeyValue' => 'abc123756%37*53f3trR3'
    ],

	# Authentication by key for Developers
	'AuthDevelopers' => [

		# The key name in headers
		'DevAuthKey' => 'X-Dev-Auth-Key',

		# The key value in headers
		'DevAuthKeyValue' => '8s79d#f798df9@78ds79f&8=79d'

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
		    'secretKey' => '~s!d8f7s#d9@f9d8%_sf9D3378Rds79#',
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
		    'account_key' => '!dFf78%6g8J9yd$fiu@ytvfj89__'
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

	# DataReception
	'DataReception' => [
		# Authentication to access this Data Reception
		'Auth' => [

			# The key name in headers
			'DRAuthKey' => 'X-Dr-Auth-Key',

			# The key value in headers
			'DRAuthKeyValue' => 'ds786f8d987789gd786fd867768sad6sda687'
		]
	],

	# DataReception Access data
	'DataReceptionAccess' => [
		# Send to
		'url' => 'http://localhost/duktig.microservice.1./www/index.php/data-reception/',

		# Authentication to access Data Reception
		'Auth' => [

			# The key name in headers
			'DRAuthKey' => 'X-Dr-Auth-Key',

			# The key value in headers
			'DRAuthKeyValue' => 'ds786f8d987789gd786fd867768sad6sda687'
		]
	],

    # Application setup configuration
    'Setup' => [
        # User accounts to generate and insert
        'UserAccounts' => [

            # How many User accounts should be created
            'GenerationCount' => 100,

            # Default Root Admin account
            'RootAccount' => [
                'firstName' => 'Root',
                'lastName' => 'Administrator',
                'email' => 'root@example.com',
                'password' => 'root@example.com.p',
                'phone' => '',
                'comment' => 'Root administrator',
                'pinCode' => '0001',
                'dateRegistered' => date('Y-m-d H:i:s'),
                'dateLastUpdate' => '',
                'dateLastLogin' => '',
                'roleId' => 1,
                'status' => 1
            ]
        ],

        # User Roles
        'userRoles' => [],
    ],

	# Backup Configuration
	'Backups' => [
		# Databases to backup
		'Databases' => [
			[
				# Database name
				'database' => '2do',
				# Database Tables which will be excluded from backup process.
				'excluded_tables' => []
			],
			[
				'database' => 'Duktig',
				'excluded_tables' => []
			],
			[
				'database' => 'DuktigAuth',
				'excluded_tables' => []
			],
			[
				'database' => 'DuktigDataReception',
				'excluded_tables' => []
			],
			[
				'database' => 'Hippo',
				'excluded_tables' => []
			],
			[
				'database' => 'duktig.dev',
				'excluded_tables' => []
			],
			[
				'database' => 'jadore',
				'excluded_tables' => []
			],
			[
				'database' => 'jadore_reports',
				'excluded_tables' => []
			],
			[
				'database' => 'sirelli',
				'excluded_tables' => []
			],
			[
				'database' => 'todo',
				'excluded_tables' => []
			]

		],
		# ! The last slash in path is important
		'DatabasesDir' => '/Users/david/Sites/duktig.microservice.1/backups/db/',
		'DatabasesBackupSteps' => 7
	]

];
