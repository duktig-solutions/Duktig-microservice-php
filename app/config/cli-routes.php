<?php
/**
 * CLI Routes configuration
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

	# Generate System Logs statistics in: app/log/stats.json
	'generateLogStats' => [
		'controller' => 'AppLogs->generateLogStats',
		'middleware' => [],
		'executeUniqueProcessLifeTime' => 10
	],
	# Archive log files if size is larger than expected
	'archiveLogFiles' => [
		'controller' => 'General\AppLogsProcessor->archiveLogs',
		'middleware' => [],
		'executeUniqueProcessLifeTime' => 10
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