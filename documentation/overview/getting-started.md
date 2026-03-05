# Duktig PHP Microservice - Development Documentation

## Getting Started

The **Duktig PHP Microservice** project provides comprehensive documentation to help developers maintain and extend the codebase.

This documentation suite explains how the project is organized and guides you through developing your own resources.

### Prerequisites

Before you start development, ensure you have the following:

- **PHP 7.4+** - The framework is built on PHP 7.4 (see [Requirements](requirements.md) for complete list)
- **Composer** - PHP dependency manager
- **Docker** - For containerized development environment
- **MySQL or PostgreSQL** - Database system (included in Docker setup)
- **Git** - Version control system
- **Code Editor** - PHPStorm, VS Code, or similar

### Project Setup

To set up your development environment:

1. Clone or fork the project repository
2. Navigate to the project root directory
3. Review [Local Development Deployment](../installation/local-dev-deployment.md) for Docker setup
4. Run `docker-compose up` to start all services
5. Access the application at `http://localhost:8088`

### Project Architecture

This project follows a pattern similar to the MVC architecture but without the View layer. Since this is a RESTful API microservice with CLI support, the focus is on HTTP routing, middleware, controllers, and data persistence layers.

The project has two main execution paths:

- **HTTP Path** - RESTful API endpoints accessed via HTTP requests
- **CLI Path** - Command-line commands executed from terminal

Both paths use the same middleware, controller, and model patterns for consistency.

### Key Concepts

Before diving into development, understand these core concepts:

- **Routes** - Configuration files that map requests to controllers
- **Middleware** - Pre-processing logic (authentication, validation, data injection)
- **Controllers** - Orchestrate business logic and coordinate system operations
- **Models** - Handle database operations and data persistence
- **Request/Response** - HTTP abstraction for handling incoming requests and outgoing responses

### Important Standards

Before you begin development, please read the [Coding Standards](coding-standards.md) document. Adhering to the project's development standards and patterns is essential for maintaining code quality and consistency.

---

## Understanding Request Flow

### HTTP Request Flow

```
Client HTTP Request
    |
    v
Nginx Reverse Proxy (src/www/index.php)
    |
    v
Route Matching (src/app/config/http-routes.php)
    |
    v
Middleware Chain Execution (in configured order)
    |  (can short-circuit with response)
    v
Controller Method Execution
    |
    v
Model/Database Operations (if needed)
    |
    v
Response Generation
    |
    v
JSON Response to Client
```

Each middleware receives the request and middleware data. If authentication fails, a middleware can send an error response and stop further processing. Otherwise, it passes data to the next middleware or controller.

### CLI Command Flow

```
CLI Command Execution
    |
    v
Route Matching (src/app/config/cli-routes.php)
    |
    v
Middleware Chain Execution
    |  (can short-circuit with output)
    v
Controller Method Execution
    |
    v
Model/Database Operations (if needed)
    |
    v
Output to Terminal
```

---

## Developing a RESTful API Endpoint

Follow these steps to create a new RESTful API resource:

1. Create or configure a new HTTP route
   - Add a new route entry in: `src/app/config/http-routes.php`
   - Configure middleware (if required)
   - Specify the controller and method

2. Create middleware (if required)
   - Add authentication, validation, or data injection logic
   - See [Middleware Documentation](../development/middleware.md) for details

3. Create a model (optional)
   - Add database operations and data access logic
   - Reuse existing models if applicable

4. Create a controller
   - Implement the business logic for the endpoint
   - Handle request and response

5. Test the endpoint
   - Verify the endpoint responds correctly
   - Test various input scenarios

### Example: Creating a Simple Endpoint

Here is a minimal example of creating a GET endpoint that returns user data:

**Step 1: Add Route** (src/app/config/http-routes.php)

```php
'GET' => [
    '/api/users/{id}' => [
        'middleware' => [],
        'controller' => 'Users\User->getById'
    ]
]
```

**Step 2: Create Controller** (src/app/Controllers/Users/User.php)

```php
<?php
namespace App\Controllers\Users;

use System\HTTP\Request;
use System\HTTP\Response;

class User {
    
    public function getById(Request $request, Response $response, array $middlewareData): bool {
        
        $userId = $request->paths(2);  // Get ID from URL path
        
        // Simulate user data
        $user = [
            'id' => $userId,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
        
        $response->sendJson([
            'status' => 'success',
            'data' => $user
        ], 200);
        
        return true;
    }
}
```

**Step 3: Test the Endpoint**

```bash
curl http://localhost:8088/api/users/1
```

Expected response:

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

For a complete step-by-step guide with database integration, see [CRUD Development Tutorial](../tutorials/crud-development.md).

---

## Creating a CLI Command

Follow these steps to create a new CLI command:

1. Create or configure a new CLI route
   - Add a new route entry in: `src/app/config/cli-routes.php`
   - Configure middleware (if required)
   - Specify the controller and method

2. Create middleware (if required)
   - Add parameter validation or pre-processing logic

3. Create a model (optional)
   - Add database operations if needed
   - Reuse existing models if applicable

4. Create a controller
   - Implement the command logic
   - Handle input parameters and output

5. Test the command
   - Execute the command from the CLI
   - Verify correct behavior and output

---

## Testing Your Endpoints

### Testing HTTP Endpoints

You can test HTTP endpoints using various tools:

**Using cURL (Command Line)**

```bash
# GET request
curl http://localhost:8088/api/endpoint

# POST request with JSON data
curl -X POST http://localhost:8088/api/endpoint \
  -H "Content-Type: application/json" \
  -d '{"key": "value"}'

# With authentication header
curl -H "X-API-Key: your-api-key" http://localhost:8088/api/endpoint
```

**Using Postman or Insomnia**

- Open the application
- Create new request
- Set method (GET, POST, etc.)
- Enter endpoint URL
- Add headers if needed
- Send request and view response

### Testing CLI Commands

Execute commands directly from the terminal:

```bash
# Test a CLI command
php /path/to/src/cli/exec.php command-name param1 param2

# From within the PHP container
docker exec duktig-php-fpm php src/cli/exec.php command-name
```

### Debugging

To troubleshoot endpoints:

1. Check application logs: `docker logs duktig-php-fpm`
2. Verify route configuration in config files
3. Check middleware execution order
4. Review controller logic
5. Inspect error responses for details

---

## Troubleshooting Common Issues

### Issue: Route Not Found (404 Error)

**Cause**: Route not configured or path doesn't match

**Solution**:
1. Verify the route is added to the correct config file (http-routes.php or cli-routes.php)
2. Check the route path matches exactly
3. Verify the controller and method name are correct
4. Restart Docker containers if you added a new route

### Issue: Middleware Blocks Request Unexpectedly

**Cause**: Middleware authentication or validation failed

**Solution**:
1. Check middleware order in route configuration
2. Review middleware logs for error details
3. Verify required headers or parameters are provided
4. Ensure credentials/tokens are valid

### Issue: Database Connection Error

**Cause**: Database service not running or wrong credentials

**Solution**:
1. Verify database container is running: `docker ps`
2. Check environment variables in docker-compose
3. Verify database name and credentials
4. Check database logs: `docker logs duktig-database-mysql`

### Issue: Class Not Found or Namespace Error

**Cause**: Incorrect namespace or class location

**Solution**:
1. Verify namespace matches directory structure
2. Check class name spelling matches filename
3. Run composer autoload: `docker exec duktig-php-fpm composer dumpautoload`
4. Verify file is in correct directory

### Issue: Permission Denied on File/Directory

**Cause**: Docker container doesn't have write permissions

**Solution**:
1. Check directory ownership in docker-compose volumes
2. Verify log directory permissions: `chmod 777 src/app/log`
3. Restart containers
4. Check Docker volume mount paths

---

## Additional Resources

Detailed documentation for developing API resources and CLI commands is available in the following guides:

### Foundation Documents

- [Architecture Overview](architecture-overview.md) - Understand system design and layers
- [Project Structure](project-structure.md) - Learn directory organization and file placement
- [Coding Standards](coding-standards.md) - Code style and conventions

### Development Guides

- [HTTP and CLI Routing](../development/http-and-cli-routing.md) - Route configuration and path matching
- [HTTP Workflow](../development/http-workflow.md) - Detailed HTTP request processing
- [CLI Workflow](../development/cli-workflow.md) - Detailed CLI command processing
- [Middleware](../development/middleware.md) - Authentication, validation, and request preprocessing

### Tutorials

- [CRUD Development Tutorial](../tutorials/crud-development.md) - Complete API development example
- [Middleware Tutorial](../tutorials/middleware.md) - Building middleware from scratch

### Setup and Configuration

- [Local Development Deployment](../installation/local-dev-deployment.md) - Set up development environment
- [Configuration and Environment Variables](../installation/env-configuration.md) - Environment setup
- [Production Deployment](../installation/production-deployment.md) - Deploy to production

### Reference

- [Requirements](requirements.md) - System requirements and versions
- [FAQ](faq.md) - Frequently asked questions

---

## Next Steps

1. Set up your local development environment using [Local Development Deployment](../installation/local-dev-deployment.md)
2. Read the [Architecture Overview](architecture-overview.md) to understand the system
3. Review [Coding Standards](coding-standards.md) before writing code
4. Follow the [CRUD Development Tutorial](../tutorials/crud-development.md) to build your first endpoint
5. Explore [Middleware Documentation](../development/middleware.md) and [Middleware Tutorial](../tutorials/middleware.md) to add pre-request processing

Happy coding!   


