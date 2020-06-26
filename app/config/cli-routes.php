<?php
/**
 * CLI Routes configuration
 */
return [
    # Setup Application
    'setup' => [
        'controller' => 'Setup->setupGeneral',
        'middleware' => [
            'Setup->setupGeneral'
        ],

	    # Execute process as unique and enable to start next process after given time in seconds.
	    # If the value is 0, then the next starting process will not check if another instance of this process is already running.
	    'executeUniqueProcessLifeTime' => 10
    ],

	# Test the setup environment.
    'envTest' => [
        'controller' => 'Setup->envTest',
        'middleware' => [],
	    'executeUniqueProcessLifeTime' => 5
    ],

	# Generate System Logs statistics in: app/log/stats.json
	'generateLogStats' => [
		'controller' => 'AppLogs->generateLogStats',
		'middleware' => [
			# This middleware class method will insert into UserActions database table the action information
			//'UserActions->cliAction'
		],
		'executeUniqueProcessLifeTime' => 10
	],
	# Archive log files if size is larger than expected
	'archiveLogFiles' => [
		'controller' => 'General\AppLogsProcessor->archiveLogs',
		'middleware' => [
			//'UserActions->cliAction'
		],
		'executeUniqueProcessLifeTime' => 10
	],
	# Generate User actions statistics
	'generateUserActionsStats' => [
		'controller' => 'UserActions->generateUserActionsStats',
		'middleware' => [
			'UserActions->cliAction'
		],
		'executeUniqueProcessLifeTime' => 100
	],
	# TEST HTTP Request in CLI mode
	'http-request' => [
		'controller' => 'Tests\Getter->cliSendRequest'
	],

	# TEST Benchmarking
	'benchmark-test-1' => [
		'controller' => 'Tests\Benchmarking->test1'
	],

	# DataCollector - Collect Exchange rate
	'dataCollectors/ExchangeRate' => [
		'middleware' => [],
		'controller' => 'dataCollector\ExchangeRate->cliCollect',
		'rolesAllowed' => []
	],

	# Backup Databases
	'db-backup' => [
		'controller' => 'Backups\DB->cliBackupDB'
	]
];