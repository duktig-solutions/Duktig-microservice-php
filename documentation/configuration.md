# Duktig.Microservice
## Documentation 

### Configuration

#### Application main configuration

The main application configuration file: `app.php` located in `app/config/` directory.

The file contains multidimentional array with configuration segments.

For instance, MySQL Databases configuration values defined in `Databases` section of file.

```php
...    
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
    ...
    ]
...    
```

Each section can contain more than one defined instances, for example, your application can access to more than one Database server or Redis instance.

It is also possible to define a new section in configuration file and get values in your code.

#### HTTP Routing

The HTTP routing file `http-routes.php` in Duktig.Microservice located in `app/config/` directlry.

You're always able to add new route, configure (edit) existing routes in your application.

Explanation of HTTP route configuration:

```php
...
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
...        
```

Working example of HTTP Routing configuration:

```php
...    
    # Example - Validate GET Request data
    '/examples/validate_get_request_data' => [
        'middleware' => [
            'Development\Auth\AuthByDeveloperKey->check'
        ],
        'controller' => 'Development\Examples\Validation->validateGetRequestData'
    ],
...        
```        

#### CLI routing

In Duktig.Microservice it is possible to configure any command line access operation in CLI routing.

For example, if you want to call Databases backup controller to run, you can configure a route in configuration file and access with one command.

CLI routing configuration file `cli-routes.php` located in `app/config/`.

Explanation of CLI routing configuration:

```php 
...
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
...    
```
Working example of CLI Routing configuration:

```php 
...
    # System - Make logs stats: app/log/stats.json
	'make-log-stats' => [
		'controller' => 'System\Logs\StatsMaker->process',
		'middleware' => [],
		'executeUniqueProcessLifeTime' => 10
	],
...
```

### Web server and php functionality

Duktig.Microservice developed and tested under Nginx web server with php-fpm service. 
It is also possible to run this project with other web server such as Apache with php module.

For performence and Docker friendly usage, we running it with Nginx Docker container with load balanced php-fpm containers.

End of Document
