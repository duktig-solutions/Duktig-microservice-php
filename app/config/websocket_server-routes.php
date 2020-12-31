<?php
/**
 * Application WebSocket Server Routing configuration file.
 * See structure and explanation in first sections.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
return [

	# Example of Web Socket server configuration

	# The key name of the array should match with the Connection configuration name in application configuration file.
	# File: app/config/app.php
	# Section: WebSocketServer
	'__ExampleSererConnection' => [

		# This is On Connection Event
		'onConnect' => [

			# Can contain Middleware
			'middleware' => [
				'__Example\__ExampleMiddlewareClass->__ExampleMethodA',
				'__Example\__ExampleMiddlewareClass->__ExampleMethodB'
			],

			# And Should contain Controller definition
			'controller' => '__Example\__ExampleControllerClass->ExampleOnConnectEventMethod'
		],

		# This is On Close Connection Event
		'onClose' => [
			# Can contain Middleware
			'middleware' => [
				'__Example\__ExampleMiddlewareClass->__ExampleMethodC',
				'__Example\__ExampleMiddlewareClass->__ExampleMethodD'
			],

			# And Should contain Controller definition
			'controller' => '__Example\__ExampleControllerClass->ExampleOnCloseEventMethod'
		],

		# Messaging with specific route
		'onMessage' => [

			# Route defined like this
			# The key in this array is the client defined route to Run.
			'/__example_Route/{eny}/{id}' => [
				# Can contain Middleware
				'middleware' => [
					'__Example\__ExampleMiddlewareClass->__ExampleMethodE',
					'__Example\__ExampleMiddlewareClass->__ExampleMethodF'
				],

				# And Should contain Controller definition
				'controller' => '__Example\__ExampleControllerClass->ExampleMessageFunctionA'
			],
			# Another route for subscription example
			'/subscribeRoom/{any}' => [
				# Contains Middleware Authentication
				'middleware' => [
					'Auth\WebSocket->AuthByClientId'
				],

				# And Should contain Controller definition
				'controller' => 'WebSocket\Chat->SubscribeForRoom'
			],
			'/chatGroupMessage/{any}' => [
				'middleware' => [
					'auth->authByClientKey'
				],
				'controller' => [
					'Events\chat->messageToGroup'
				]
			]
		]
	],

	# Chat Web Socket
	'Chat' => [
		'onConnect' => [

			# Can contain Middleware
			'middleware' => [
				'Auth\WebSocket->justInfoInjected'
			],

			# And Should contain Controller definition
			'controller' => 'Auth\WebSocket->onConnect'
		],

	]

];
