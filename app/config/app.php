<?php
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
    'ProjectName' => 'Duktig.Microservice',

    # Your project version
    'ProjectVersion' => '1.0.0',

	# Microservice ID (Aka System id) for each instance.
	# i.e. Accounts | Reports | DataReception | Notes, etc ...
	'Microservice' => 'Development',

    # Log errors. All type of error logs located in: /app/log
    'LogErrors' => 1,

    # Display errors configuration flag for php.ini
    'DisplayErrors' => 1,

    # Default date time zone for application
	# America/Los_Angeles
    'DateTimezone' => 'Asia/Yerevan',

    # Application mode can be: production | development and others
    # If it is production mode, detailed error messages will not be displayed in Response json.
    'Mode' => 'development',

    # If application is under maintenance any Request will be
    # Responded with 503 Status (Service not available).
    'UnderMaintenance' => 0,

    # Enable or disable CLI mode.
    # In some cases you can temporarily disable CLI functionality.
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

	    # Credentials for Database backup - MySQL Server root user account
	    'BackupConn' => [
		    'driver' => 'MySQLi',
		    'host' => 'localhost',
		    'port' => 3306,
		    'username' => 'root',
		    'password' => 'abc123',
		    'database' => 'Duktig',
		    'charset' => 'utf8'
		],

		# Credentials for PostgreSQL Connection
		'PostgreSQL_Cred' => [
			'driver' => 'PostgreSQL',
			'host' => '192.168.0.132',
			'port' => 5433,
			'database' => 'Warehouse',
			'username' => 'postgres',
			'password' => 'warehouse123',
			'client_encoding' => 'UTF8'
		]
    ],

	# Redis Connection configuration
    'Redis' => [
        
		# Intermediate Data Center for Microservices (misc)
		'IntermediateDataCenter' => [
			'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6380,
            'database' => 0,
            'read_write_timeout' => 0,
            'password' => 're2020Duk_psGw',
            'queueName' => 'MQ_d876g66886gfd'
		],

		# Message/Queue For development purposes
		'MessageQueue' => [
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6380,
            'database' => 2,
            'read_write_timeout' => 0,
            'password' => 're2020Duk_psGw',
            'queueName' => 'MQ_d876g66886gfd',
            'task_execution_attempts' => 5
        ],

		# System Data Caching for development purposes
		'DevelopmentTestSystemCaching' => [
			'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6380,
            'database' => 3,
            'read_write_timeout' => 0,
            'password' => 're2020Duk_psGw',
            'cache_expiration_seconds' => 3 // (5 minute = 300 seconds)
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

	# System binary executables
	'Executables' => [
		'curl' => '/usr/bin/curl',
		'mysqldump' => '/usr/bin/mysqldump'
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

		# How many copies will keep the backup.
		# for instance, setting number 7 will assume that the backup will remove old backups after 7 copies.
		# And the 7 means to keep backed up databases for 7 days and remove the old 8th.
		'DatabasesBackupSteps' => 7
	],

];
