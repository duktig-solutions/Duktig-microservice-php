<?php
/**
 * Application HTTP Routing configuration file.
 * See structure and explanation in the first sections.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
//echo $_SERVER['REQUEST_URI']; exit();
return [

	# Example: Template of Route configuration

    # Request method
    # Can be any HTTP Request method such as: GET, POST, PUT, PATCH, DELETE, etc...
    # Each Request method can contain one or more Request responses.
    # i.e. GET: /user, GET: /user/33, GET: /user/33/comments etc...
    'GET' => [

        # Route
        # Here you can describe route paths for request.
        # Each item in route path can contain exact matching words i.e. /user or variables.
        # Variables can be:
        #   {id} - only ID number
        #   {num} - only numeric
        #   {any} - type of string
        #
        # For example, this route path accepts only ID integer for second item: /example/123 (correct). /example/something (not correct).
        # If the route path does not match, the system will respond with Error 404 (Resource not found).
        '/__example/{id}/posts/{any}' => [

            # Middleware
            # With The middleware option, you can set any number of middleware methods before the controller starts.
            # For instance, you can make middleware methods to: Authorize a client, Validate Request data, then continue to controller.
            # The middleware functionality also can be used to get response data from cache instead of Controller -> Model -> Database.
            # The format of middleware configuration is: ClassName->methodName where the middleware classes are located in /app/middleware directory.
            # Note: If you not have any middleware functionality for this route, you can just pass this section as empty.
            'middleware' => [
                '___ExampleMiddlewareClass->exampleMiddlewareMethod',
	            '___AnotherExampleMiddlewareClass->anotherExampleMiddlewareMethod'
            ],

            # Controller
            # Controllers runs as regular controllers in MVC Pattern.
            # The format of controller configuration is: ClassName->methodName where the controller classes are located in /app/controllers directory.
            # Note: You can put authorization, caching, data validation functionality inside a controller method instead of creating dedicated middleware for it.
            'controller' => '___ExampleControllerClass->exampleControllerMethod',

			# Required permission(s) to access this resource. 
			# Listed as Microservice->Perm1, Perm2
			# NOTICE: If this item in array is missed, this will assume any type of User can access to this resource.

			# WARNING !!! The array "permissionsRequired" works paired with middleware you're specified.
	        # So you have to check permissions by yourself as it does middleware: Auth->Authenticate			
			// @todo
			// Make permissions functionality
			'permissionsRequired' => [
				
				# Microservice name => Permission Ids
				'Accounts' => [
					
					# Permissions defined in app/config/constants.php
				//	PERMISSIONS['Accounts']['Account']['patch'],
					
					# It is also possible to add more than one permission to a requirement list,
					# So the system will check more than one permission to allow accessing a resource
				//	PERMISSIONS['Accounts']['Account']['delete']
				]
			],

	        # This Route configured to cache response data by system
	        # Just putting the configuration name here and all will work automatically.
	        # The caching configuration specified in application config file.
	        'cacheConfig' => '___ResponseDataCaching'

        ],

		# ------------------------------------------------- #
	    # ---------- Development Examples ----------------- #
		# ------------------------------------------------- #

	    # Example - Response send file (Download a file)
	    '/examples/get-file' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\Getter->downloadFile'
	    ],

	    # Example - Validate GET Request data
	    '/examples/validate_get_request_data' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\Validation->validateGetRequestData'
	    ],

	    # Example - Get Cached data by system with only configured in route
	    '/examples/get_cached' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\DataCaching->httpTestSystemCaching',

		    # This Route configured to cache response data by system
		    # Just putting the configuration name here and all will work automatically.
			# In the application configuration, this directive is defined in the "Redis" section.
		    'cacheConfig' => 'SystemCaching'
	    ],

	    # Example - Get Custom Cached data using middleware
	    '/examples/get_custom_cached' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check',
		    	'Development\Examples\DataCaching->responseFromCache',
		    ],
		    'controller' => 'Development\Examples\DataCaching->httpTestManualCaching'
		    # As you see, this Route not has configured caching.
		    # The caching will work programmatically by Middleware and Controller.
	    ],

	    # Example - Using System Libraries
	    '/examples/use_system_libraries' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\LibrariesUsage->useSystemLibrary'
	    ],

	    # Example - Using Application custom Library
	    '/examples/use_application_libraries' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\LibrariesUsage->useApplicationLibrary'
	    ],

	    # Example - Using Application custom Library extended from system Library
	    '/examples/use_application_extended_libraries' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\LibrariesUsage->useApplicationExtendedLibrary'
	    ],

		# Example - Get Client Information
		'/examples/client_info' => [
		    'middleware' => [
			    //'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\ClientInfo->dump'
	    ],

        # Example - Response All possible request Data
        '/examples/response_all_environment_variables' => [
            'middleware' => [
                'Development\Auth\AuthByDeveloperKey->check'
            ],
            'controller' => 'Development\Examples\Getter->responseAllEnvironmentVariables'
        ],

	    # System - Get System Logs Statistics
	    'system/app_log_stats' => [
		    'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'System\Logs\StatsMaker->get'
	    ],
		
	    # System - Ping The system.
	    # Will response plain text: Pong
	    # For now this resource not requires authentication
	    'system/ping' => [
		    'middleware' => [],
		    'controller' => 'System\HealthCheck\SystemHealthCheck->ping'
	    ]
		
    ],
    'POST' => [
		
		# ------------------------------------------------- #
	    # ----------------- Examples ---------------------- #
		# ------------------------------------------------- #

		# Example - Response All possible request Data
	    '/examples/response_all_request_data/{id}/{num}/{any}' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check',
			    'Development\Examples\Injection->injectMiddlewareData'
		    ],
		    'controller' => 'Development\Examples\Getter->responseAllRequestData'
	    ],

    	# Example - Validate Request Json as array
	    '/examples/validate_array_from_json' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\Validation->validateRequestArrayFromJson'
	    ],

	    # Example - Validate Request Multidimensional Json as array
	    '/examples/validate_multidimensional_array_from_json' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\Validation->validateRequestMultiDimensionalArrayFromJson'
	    ],

	    # Example - Validate Request Form data
	    '/examples/validate_form_data' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check'
		    ],
		    'controller' => 'Development\Examples\Validation->validateFormRequest'
	    ],

		# Example - Test PostgreSQL class functionality
		'/examples/test_postgres' => [
			'controller' => 'Development\Examples\PostgreSqlTests->run'
		],

    ],
    'PUT' => [


    ],
    'PATCH' => [

    ],
    'DELETE' => [

    ]
];
