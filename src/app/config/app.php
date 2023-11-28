<?php

use System\Env;

/**
 * Application Main Configuration file.
 * See structure and explanation bellow.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
return [

    # Your project/setup name
    'ProjectName' => Env::get('PROJECT_NAME'),

	# Microservice ID (Aka System id) for each instance.
	# I.e., Accounts | Reports | DataReception | Notes, etc ...
	'Microservice' => Env::get('MICROSERVICE_ID'),

    // Allowed hosts
    'AllowedHosts' => Env::get('ALLOWED_HOSTS'),

    # Log errors. All type of error logs located in: /app/log
    'LogErrors' => Env::get('LOG_ERRORS'),

    # Display errors a configuration flag for php.ini
    'DisplayErrors' => Env::get('DISPLAY_ERRORS'),

    # Default date time zone for application
	# America/Los_Angeles
    # Asia/Yerevan
    'DateTimezone' => Env::get('DATE_TIME_ZONE'),

    # Application mode can be: production | development and others
    # If it is a production mode, detailed error messages will not be displayed in Response json.
    'Mode' => Env::get('RUN_MODE'),

    # If application is under maintenance any Request will be
    # Responded with 503 Status (Service not available).
    'UnderMaintenance' => Env::get('UNDER_MAINTENANCE'),

    # Enable or disable CLI mode.
    # In some cases, you can temporarily disable CLI functionality.
    # Set this to "0" to stop Cli route parsing and functionality.
    'DisableCLI' => Env::get('DISABLE_CLI'),

    # Database Connection Configuration
    # Each model in /app/models is able to use/start with one connection section.
    'Databases' => [

	    # Section name defined by Developer will be used in model file.
	    'Example_MySQL_SERVER_Connection' => [

		    # Database Driver Class can be defined as MySQLi
		    'driver' => 'MySQLi',
		    # Host
		    'host' => Env::get('EXAMPLE_MYSQL_HOST'),
		    # Port
		    'port' => Env::get('EXAMPLE_MYSQL_PORT'),
		    # MySQL Username
		    'username' => Env::get('EXAMPLE_MYSQL_USER'),
		    # MySQL Password
		    'password' => Env::get('EXAMPLE_MYSQL_PASSWORD'),
		    # MySQL Database name
		    'database' => Env::get('EXAMPLE_MYSQL_DATABASE'),
		    # Charset
		    'charset' => Env::get('EXAMPLE_MYSQL_CHARSET')
	    ],

	    # Credentials for Database backup - MySQL Server root user account
	    'MySQLBackupConn' => [
		    'driver' => 'MySQLi',
		    'host' => Env::get('EXAMPLE_MYSQL_HOST'),
		    'port' => Env::get('EXAMPLE_MYSQL_PORT'),
            'username' => Env::get('EXAMPLE_MYSQL_USER'),
            'password' => Env::get('EXAMPLE_MYSQL_PASSWORD'),
            'database' => Env::get('EXAMPLE_MYSQL_DATABASE'),
            'charset' => Env::get('EXAMPLE_MYSQL_CHARSET')
		],

		# Credentials for PostgreSQL Connection
		'Example_PostgreSQL_SERVER_Connection' => [
			'driver' => 'PostgreSQL',
			'host' => Env::get('EXAMPLE_POSTGRESQL_HOST'),
			'port' => Env::get('EXAMPLE_POSTGRESQL_PORT'),
			'database' => Env::get('EXAMPLE_POSTGRESQL_DATABASE'),
			'username' => Env::get('EXAMPLE_POSTGRESQL_USER'),
			'password' => Env::get('EXAMPLE_POSTGRESQL_PASSWORD'),
			'client_encoding' => Env::get('EXAMPLE_POSTGRESQL_CLIENT_ENCODING')
		]
    ],

	# Redis Connection configuration
    'Redis' => [
        
		# Events Pub/Sub for Development purposes
		'EventsPubSub' => [
			'scheme' => Env::get('REDIS_EVENTS_PUB_SUB_SCHEME'),
            'host' => Env::get('REDIS_EVENTS_PUB_SUB_HOST'),
            'port' => Env::get('REDIS_EVENTS_PUB_SUB_PORT'),
            'database' => Env::get('REDIS_EVENTS_PUB_SUB_DATABASE'),
            'read_write_timeout' => Env::get('REDIS_EVENTS_PUB_SUB_READ_WRITE_TIMEOUT'),
            'password' => Env::get('REDIS_EVENTS_PUB_SUB_PASSWORD'),
            'channel' => Env::get('REDIS_EVENTS_PUB_SUB_CHANNEL')
		],

		# Message/Queue For development purposes
		'MessageQueue' => [
            'scheme' => Env::get('REDIS_MQ_SCHEME'),
            'host' => Env::get('REDIS_MQ_HOST'),
            'port' => Env::get('REDIS_MQ_PORT'),
            'database' => Env::get('REDIS_MQ_DATABASE'),
            'read_write_timeout' => Env::get('REDIS_MQ_READ_WRITE_TIMEOUT'),
            'password' => Env::get('REDIS_MQ_PASSWORD'),
            'queueName' => Env::get('REDIS_MQ_QUEUE_NAME'),
            'task_execution_attempts' => Env::get('REDIS_MQ_TASK_EXEC_ATTEMPTS')
        ],

		# System Data Caching for development purposes
		'SystemCaching' => [
			'scheme' => Env::get('REDIS_CACHING_SCHEME'),
            'host' => Env::get('REDIS_CACHING_HOST'),
            'port' => Env::get('REDIS_CACHING_PORT'),
            'database' => Env::get('REDIS_CACHING_DATABASE'),
            'read_write_timeout' => Env::get('REDIS_CACHING_READ_WRITE_TIMEOUT'),
            'password' => Env::get('REDIS_CACHING_PASSWORD'),
            'cache_expiration_seconds' => Env::get('REDIS_CACHING_EXPIRATION_SECS') // (5 minutes = 300 seconds)
		]
    ],

    # Authentication by key
    'Auth' => [

        # The key name in headers
        'AuthKey' => Env::get('AUTH_BY_HEADER_KEY_NAME'),

        # The key value in headers
        'AuthKeyValue' => Env::get('AUTH_BY_HEADER_KEY_VALUE')

    ],

    'JWT' => [
        'iss' => 'DMD1',
        'aud' => 'DMD2',
        'sub' => 'DMD3',
        'jti' => 'DMD4',

        # Not before
        'nbf' => '-1 day',

        # Issued at
        'iat' => time(),

        # Token expiration time
        'exp' => '+1 days',

        'secretKey' => '63F6B159256D2DA6BBB687FA22A4A52AAFC301007E1BB388937112C13037C518'
    ],

	# Authentication by key for Developers
	'AuthDevelopers' => [

		# The key name in headers
		'DevAuthKey' => Env::get('AUTH_DEV_BY_HEADER_KEY_NAME'),

		# The key value in headers
		'DevAuthKeyValue' => Env::get('AUTH_DEV_BY_HEADER_KEY_VALUE')

	],

	# System binary executables
	'Executables' => [
		'curl' => Env::get('EXECUTABLE_CURL'),
		'mysqldump' => Env::get('EXECUTABLE_MYSQLDUMP')
	],

	# Backup Configuration
	'Backups' => [

        # Databases to backup
		'Databases' => [
			
			[
				'database' => 'duktig.dev',
				'excluded_tables' => []
			],
			
			[
				'database' => 'DuktigUsers',
				'excluded_tables' => []
			]
		],

		# Path where to save backup database files
		# If this value is empty, the system will automatically back up into ./backups/db/
		# Notice: The last slash is important if you specify a path.
		'DatabasesBackupDir' => '',

		# How many copies will keep the backup?
		# For instance, setting number 7 will assume that the backup will remove old backups after 7 copies.
		# And the 7 means to keep backed-up databases for 7 days and remove the old 8th.
		'DatabasesBackupSteps' => 7
	],

];
