<?php
/**
 * Application HTTP Routing configuration file.
 * See structure and explanation in first sections.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
return [

	# Example: Template of Route configuration

    # Request method
    # Can be any HTTP Request method such as: GET, POST, PUT, PATCH, DELETE, etc...
    # Each Request method can contain one or more Request responses.
    # i.e. GET: /user, GET: /user/33, GET: /user/33/comments etc...
    'GET' => [

        # Route
        # Here you can describe route paths for request.
        # Each route path items can contain exact matching words i.e. /user or variables.
        # Variables can be:
        #   {id}  - only ID number
        #   {num} - only numeric
        #   {any} - type of string
        #
        # For example, this route path accepts only ID integer for second item: /example/123 (correct). /example/something (not correct).
        # If the route path not matches, the system will response with Error 404 (Resource not found).
        '/example/{id}/posts/{any}' => [

            # Middleware
            # With The middleware option you can set any number of middleware methods before the controller starts.
            # For instance, you can make middleware methods to: Authorize client, Validate Request data, then continue to controller.
            # The middleware functionality also can be used to get response data from cache instead of Controller -> Model -> Database.
            # The format of middleware configuration is: ClassName->methodName where the middleware classes located in /app/middleware directory.
            # Note: If you not have any middleware functionality for this route, you can just pass this section as empty.
            'middleware' => [
                '___ExampleMiddlewareClass->exampleMiddlewareMethod',
	            '___AnotherExampleMiddlewareClass->anotherExampleMiddlewareMethod'
            ],

            # Controller
            # Controllers runs as regular controllers in MVC Pattern.
            # The format of controller configuration is: ClassName->methodName where the controller classes located in /app/controllers directory.
            # Note: You can put authorization, caching, data validation functionality inside a controller method instead of creating a dedicated middleware for it.
            'controller' => '___ExampleControllerClass->exampleControllerMethod',

	        # List of User Roles Allowed to access to this resource.
	        # NOTICE: If this item in array is missed, this will assume any type of User can access to this resource.
	        'rolesAllowed' => [
		        # Access granted to any type of User.
	        	USER_ROLE_ANY,

		        # Super Admin
		        USER_ROLE_SUPER_ADMIN,

		        # Admin
		        USER_ROLE_ADMIN,

		        # Service Provider
		        USER_ROLE_SERVICE_PROVIDER,

		        # Client
		        USER_ROLE_CLIENT,

		        # Developers to access this resource for some purposes.
		        USER_ROLE_DEVELOPER
	        ],

	        # WARNING !!! The array "rolesAllowed" works paired with middleware you're specified.
	        # In other words, you have to check permissions by yourself as it does middleware: Auth->Authenticate

	        # This Route configured to cache response data by system
	        # Just putting the configuration name here and all will work automatically.
	        # The caching configuration specified in application config file.
	        'cacheConfig' => '___ResponseDataCaching'

        ],

	    # Development Examples

	    # Example - Response send file (Download a file)
	    '/examples/get-file' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey'
		    ],
		    'controller' => 'Examples\Getter->downloadFile'
	    ],

	    # Example - Validate GET Request data
	    '/examples/validate_get_request_data' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey'
		    ],
		    'controller' => 'Examples\Validation->validateGetRequestData'
	    ],

	    # Example - Get Cached data by system with only configured in route
	    '/examples/get_cached' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey',
		    ],
		    'controller' => 'Examples\DataCaching->getCached',

		    # This Route configured to cache response data by system
		    # Just putting the configuration name here and all will work automatically.
		    'cacheConfig' => 'ResponseDataCaching'
	    ],

	    # Example - Get Custom Cached data using middleware
	    '/examples/get_custom_cached' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey',
		    	'Examples\Memcached->cachedResponse',
		    ],
		    'controller' => 'Examples\DataCaching->getCustomCached'
		    # As you see, this Route not have configured caching.
		    # The caching will work programmatically by Middleware and Controller.

	    ],

        # - Get Logged in User account
        '/user' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\User->getAccount',
	        'rolesAllowed' => [
		        USER_ROLE_ANY
	        ]
        ],
        # - Get Users
	    '/users' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\Users->getAccounts',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ],
	    # - Get user by id
        '/users/{id}' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\Users->getAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ],

	    # Get System Logs Statistics
	    'system/app_log_stats' => [
		    'middleware' => [
			    'Auth\Auth->Authenticate'
		    ],
		    'controller' => 'System\AppLogsProcessor->getLogStats',
		    'rolesAllowed' => [
			    USER_ROLE_SUPER_ADMIN
		    ]
	    ],

	    # Ping The system.
	    # Will response plain text: Pong
	    # For now this resource not requires authentication
	    'system/ping' => [
		    'middleware' => [],
		    'controller' => 'System\SystemHealthCheck->ping',
		    'rolesAllowed' => []
	    ]
    ],
    'POST' => [

    	# Development Examples

    	# Example - Response All possible request Data
	    '/examples/response_all_request_data' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey',
			    'Tests\Test->injectMiddlewareData'
		    ],
		    'controller' => 'Examples\Getter->responseAllRequestData'
	    ],

    	# + Test - validate Request Json as array
	    '/tests/validate_array_from_json' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey'
		    ],
		    'controller' => 'Tests\Validation->validateRequestArrayFromJson'
	    ],

	    # + Test - validate Request Multidimensional Json as array
	    '/tests/validate_multidimensional_array_from_json' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey'
		    ],
		    'controller' => 'Tests\Validation->validateRequestMultiDimensionalArrayFromJson'
	    ],

	    # + Test - validate Request Form data
	    '/tests/validate_form_data' => [
		    'middleware' => [
			    'Auth\Developer->AuthByDeveloperKey'
		    ],
		    'controller' => 'Tests\Validation->validateFormRequest'
	    ],

    	# + Authorize user and get token
    	'/auth/token' => [
            'middleware' => [
                'Auth\Auth->AuthByKey'
            ],
            'controller' => 'Auth\Auth->Authorize'
        ],
	    # + Refresh authorization token
	    '/auth/refresh_token' => [
		    'middleware' => [
			    'General\Auth->AuthenticateRefreshToken'
		    ],
		    'controller' => 'Auth\Auth->RefreshToken'
	    ],
	    # + Sign up
	    '/user' => [
            'middleware' => [
	            'Auth\Auth->AuthByKey'
            ],
            'controller' => 'Auth\User->registerAccount'
        ],
	    # + Register user account
        '/users' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\Users->registerAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ],

	    # =-=-=-=-=-=-=-=-=
	    # DataReception
	    # =-=-=-=-=-=-=-=-=

	    '/data-reception/{any}' => [
		    'middleware' => [
			    'DataReception\Auth->AuthByKey',
			    'DataReception\SystemDetector->detectByPaths'
		    ],
		    'controller' => 'DataReception\DataReceiverGeneral->receive',
		    'rolesAllowed' => [],
		    'allowedSystemIds' => [
		    	'ExchangeRate.py',
			    'ExchangeRate.php',
			    'Hotel.py',
			    'MicroservicesLookup',
			    'WebsitesLookupForScraping'
		    ]
	    ]
    ],
    'PUT' => [
	    # + Update User account (All data)
    	'/user' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\User->updateAccount',
		    'rolesAllowed' => [
			    USER_ROLE_ANY
		    ]
        ],
        # + Update user account
	    '/users/{id}' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\Users->updateAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ]
    ],
    'PATCH' => [
	    # + Update User account (parts)
    	'/user' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\User->patchAccount',
		    'rolesAllowed' => [
			    USER_ROLE_ANY
		    ]
        ],
	    # + Patch user account
	    '/users/{id}' => [
		    'middleware' => [
			    'General\Auth->Authenticate'
		    ],
		    'controller' => 'Auth\Users->patchAccount',
		    'rolesAllowed' => [
			    USER_ROLE_SUPER_ADMIN
		    ]
	    ]
    ],
    'DELETE' => [
    	# + Terminate user account
        '/users/{id}' => [
            'middleware' => [
                'General\Auth->Authenticate'
            ],
            'controller' => 'Auth\Users->terminateAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ]
    ]
];
