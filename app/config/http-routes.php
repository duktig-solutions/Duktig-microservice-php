<?php
/**
 * Application HTTP Routing configuration file.
 * See structure and explanation in first sections.
 *
 * @author David A. <software@duktig.dev>
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
        # Each route path items can contain exact matching words i.e. /user or variables.
        # Variables can be:
        #   {id}  - only ID number
        #   {num} - only numeric
        #   {any} - type of string
        #
        # For example, this route path accepts only ID integer for second item: /example/123 (correct). /example/something (not correct).
        # If the route path not matches, the system will response with Error 404 (Resource not found).
        '/__example/{id}/posts/{any}' => [

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

			# Required permission(s) to access this resource. 
			# Listed as "Microservice->Perm1, Perm2
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
					
					# It is also possible to add more than one permission to requirement list
					# So the system will check more than one permission to allow access a resource
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
			# In the application configuration this directive defined in "Redis" section.
		    'cacheConfig' => 'DevelopmentTestSystemCaching'
	    ],

	    # Example - Get Custom Cached data using middleware
	    '/examples/get_custom_cached' => [
		    'middleware' => [
			    'Development\Auth\AuthByDeveloperKey->check',
		    	'Development\Examples\DataCaching->responseFromCache',
		    ],
		    'controller' => 'Development\Examples\DataCaching->httpTestManualCaching'
		    # As you see, this Route not have configured caching.
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

		# ------------------------------------------------- #
	    # ---------- Authorization service----------------- #
		# ------------------------------------------------- #
		
		'/auth' => [
			'middleware' => [],
		    'controller' => 'Auth\Auth->perform'
		],

		# ------------------------------------------------- #
	    # ---------- Account microservice ----------------- #
		# ------------------------------------------------- #

		# User Account Get profile data
		'/account' => [
		    'middleware' => [
			    'Accounts\Account\InjectHeaderAccountInfo->injectFromHeaders'
		    ],
		    'controller' => 'Accounts\Account\Account->get'
	    ],

		# Get Account active Logins
		'/account/sessions' => [
			'middleware' => [
			    'Accounts\Account\InjectHeaderAccountInfo->injectFromHeaders'
		    ],
		    'controller' => 'Accounts\Account\Account->getLoginSessions'
		],

		# AccountsManagement Get List of accounts
		'/accounts' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Accounts->getAll'
		],

		# AccountsManagement Get specified account
		'/accounts/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Accounts->getByIdStr'
		],

		# Get Accounts data as Profiles list.
		# The case when loading comments from "Comments" microservice and need to get users information by userIds
		'/profiles' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\Profiles\Profiles->getAllByIds',			
			# This resource uses System caching
			'cacheConfig' => 'DevelopmentTestSystemCaching'
		],
		
		# Get Account data as Profile details.
		# The case when visiting to a user page/profile and have to see more info.
		'/profiles/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\Profiles\Profiles->getById',	
			# This resource uses System caching
			'cacheConfig' => 'DevelopmentTestSystemCaching'
		],

		# Get Permissions tree with Microservice->Permissions
		'/permissions' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Permissions->getAll'
		],

		# Get all Roles
		'/roles' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Roles->getAll'
		],

		# Get specified Role
		'/roles/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Roles->getById'
		],

		/* ========================= OLD VERSION ================================ */

        # User - Get Authorized User account
        /*
		'/user' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'User\User->getAccount',
	        'rolesAllowed' => [
		        USER_ROLE_ANY
	        ]
        ],

	    # Users - Get Users list
	    '/users' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'Users\Users->getAccounts',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ],
	    # Users - Get user by id
        '/users/{id}' => [
            'middleware' => [
	            'Auth\Auth->Authenticate'
            ],
            'controller' => 'Users\Users->getAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ],
		*/

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

		# Account signup (with Email and Password)
		'/signup' => [
			'middleware' => [
                'General\Auth\AuthByKey->check'
            ],
            'controller' => 'Accounts\Account\Signup->process'
		],

		# Signup by FireBase Token
		'/signup_firebase' => [
			'middleware' => [
                'General\Auth\AuthByKey->check'
            ],
            'controller' => 'Accounts\Account\SignupFireBase->process'
		],

		# Signup by Facebook
		'/signup_facebook' => [
			'middleware' => [
                'General\Auth\AuthByKey->check'
            ],
            'controller' => 'Accounts\Account\SignupFacebook->process'
		],

		# Signup by Facebook
		'/signup_google' => [
			'middleware' => [
                'General\Auth\AuthByKey->check'
            ],
            'controller' => 'Accounts\Account\SignupGoogle->process'
		],

		# Signup by Facebook
		'/signup_apple' => [
			'middleware' => [
                'General\Auth\AuthByKey->check'
            ],
            'controller' => 'Accounts\Account\SignupApple->process'
		],
		
		# Account signin (with Email and Password)
		'/signin' => [
			'middleware' => [
                'General\Auth\AuthByKey->check'
            ],
            'controller' => 'Accounts\Account\Signin->process'
		],

		# Account Refresh token
		'/refresh_token' => [
			'middleware' => [
                'Accounts\Account\AuthByRefreshToken->check'
            ],
            'controller' => 'Accounts\Account\RefreshToken->process'
		],

		# Create Account by Admin
		'/accounts' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
			'controller' => 'Accounts\AccountsManagement\Accounts->create'
		],

		# Create a role
		'/roles' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Roles->create'
		],

		/* ========================= OLD VERSION ============================== */

		/*
    	# Auth - Authorize user by email/password and get token
    	'/auth/login' => [
            'middleware' => [
                'Auth\Auth->AuthByKey'
            ],
            'controller' => 'Auth\Auth->Login'
        ],

	    # Auth - Refresh authorization token
	    '/auth/refresh_token' => [
		    'middleware' => [
			    'Auth\Auth->AuthenticateRefreshToken'
		    ],
		    'controller' => 'Auth\Auth->RefreshToken'
	    ],

	    # User - Register an account / Simple Sign up
	    '/user' => [
            'middleware' => [
	            'Auth\Auth->AuthByKey'
            ],
            'controller' => 'User\User->registerAccount'
        ],

	    # Users - Create/Register a user account
        '/users' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'Users\Users->registerAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ]
		*/

    ],
    'PUT' => [

		/* ========================= OLD VERSION ============================== */

		/*
    	# User - Update Authorized User account (All data)
    	'/user' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'User\User->updateAccount',
		    'rolesAllowed' => [
			    USER_ROLE_ANY
		    ]
        ],

        # Users - Update a user account
	    '/users/{id}' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'Users\Users->updateAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ]

		*/

    ],
    'PATCH' => [

		# Logged in user - Patch profile data
		'/account' => [
		    'middleware' => [
			    // 'General\Auth\AuthByToken->check',
				// 'Accounts\Account\VerifyAccountDbStatus->getVerify'
				'Accounts\Account\InjectHeaderAccountInfo->injectFromHeaders',
				'Accounts\Account\VerifyAccountDbStatus->getVerify'
		    ],
		    'controller' => 'Accounts\Account\Account->patch'
	    ],

		# AccountsManagement Patch specified account data 
		'/accounts/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Accounts->patch'
		],

		# Patch a role
		'/roles/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Roles->patch'
		],

		/* ========================= OLD VERSION ============================== */

		/*
    	# User - Update User account (parts)
    	'/user' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'User\User->patchAccount',
		    'rolesAllowed' => [
			    USER_ROLE_ANY
		    ]
        ],

	    # Users - Update a user account (parts)
	    '/users/{id}' => [
		    'middleware' => [
			    'Auth\Auth->Authenticate'
		    ],
		    'controller' => 'Users\Users->patchAccount',
		    'rolesAllowed' => [
			    USER_ROLE_SUPER_ADMIN
		    ]
	    ]

		*/

    ],
    'DELETE' => [

		# User Account Get profile data
		'/account' => [
		    'middleware' => [
			    'General\Auth\AuthByToken->check',
				'Accounts\Account\VerifyAccountDbStatus->getVerify'
		    ],
		    'controller' => 'Accounts\Account\Account->delete'
	    ],

		# Delete specified logged in session (token)
		'/account/sessions/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check',
				'Accounts\Account\VerifyAccountDbStatus->getVerify'
		    ],
		    'controller' => 'Accounts\Account\Account->deleteLoginSession'
		],

		# Delete all logged in sessions (tokens)
		'/account/sessions' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check',
				'Accounts\Account\VerifyAccountDbStatus->getVerify'
		    ],
		    'controller' => 'Accounts\Account\Account->deleteAllLoginSessions'
		],

		# AccountsManagement Terminate specified account data 
		'/accounts/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Accounts->delete'
		],

		# Delete role
		'/roles/{any}' => [
			'middleware' => [
			    'General\Auth\AuthByToken->check'
		    ],
		    'controller' => 'Accounts\AccountsManagement\Roles->delete'
		],

		/* ========================= OLD VERSION ============================== */

		/*
    	# Users - Terminate user account
        '/users/{id}' => [
            'middleware' => [
                'Auth\Auth->Authenticate'
            ],
            'controller' => 'Users\Users->terminateAccount',
	        'rolesAllowed' => [
		        USER_ROLE_SUPER_ADMIN
	        ]
        ]
		*/

    ]
];
