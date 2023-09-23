<?php
/**
 * Application CLI Routes configuration file.
 * See structure and explanation bellow.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
return [

	# Example: Template of Route configuration

	# Route to access in CLI.
	# For instance: php /duktig.solutions.1/cli/exec.php example-route --parameter1name parameter1value
	'example-route' => [

		# Middleware
		# With The middleware option you can set any number of middleware methods before the controller starts.
		# For instance, you can make middleware methods to: Check/validate command line parameters then continue to controller.
		# The format of middleware configuration is: ClassName->methodName where the middleware classes located in /app/middleware directory.
		# Note: If you not have any middleware functionality for this route, you can just pass this section as empty.
		'middleware' => [
			'___ExampleMiddlewareClass->exampleMiddlewareCliMethod'
		],

		# Controller
		# Controllers runs as a regular function like in web MVC Pattern.
		# The format of controller configuration is: ClassName->methodName where the controller classes located in /app/controllers directory.
		# Note: You can put caching, data validation and other functionality inside a controller method instead of creating a dedicated middleware for it.
		'controller' => '___ExampleControllerClass->exampleControllerCliMethod',

		# Execute process as unique and enable to start next process after given time in seconds.
		# If the value is 0, the next starting process will run immediately without checking if another instance is in process.
		'executeUniqueProcessLifeTime' => 10

		# Unlike Restful API interface the Command line interface doesn't support authorization/permission checking and caching functionality.
	],

    # ---- Log file processing ------

	# System - Archive log files if size is larger than expected
	'archive-log-files' => [
		'controller' => 'System\Logs\Archiver->process',
		'middleware' => [],
		'executeUniqueProcessLifeTime' => 10
	],

	# System - Make logs stats: app/log/stats.json
	'make-log-stats' => [
		'controller' => 'System\Logs\StatsMaker->process',
		'middleware' => [],
		'executeUniqueProcessLifeTime' => 10
	],

    # ------ Database backup --------

	# Backup Databases
	'db-backup' => [
		'controller' => 'System\Backups\DB->cliBackupDB'
	],

	# ------ Development examples --------

	# Example - Benchmarking
	'benchmarking-example-cli' => [
		'controller' => 'Development\Examples\Benchmarking->presentInCli'
	],

	# Example - Send HTTP Request in CLI mode
	'http-request-cli' => [
		'controller' => 'Development\Examples\Getter->cliSendHttpRequest'
	],

	# Example of Data Caching with Redis in command line
	'development-test-redis-caching' => [
		'controller' => 'Development\Examples\DataCaching->cliTestCaching'
	],

    # ------ Message/Queue --------

    # Message/Queue Producer
    'development-mq-producer-test' => [
        'controller' => 'Development\MessageQueue\TestProducer->produce'
    ],

    # Message/Queue Consumer
    'development-mq-consumer' => [
        'controller' => 'Development\MessageQueue\Consumer->consume'
    ]


];