# Duktig PHP Microservice - Development Documentation

## Tutorial - Middleware Creation

This tutorial demonstrates how to create, configure, and use custom middleware in the Duktig PHP Microservice Framework. We'll build several types of middleware from simple to advanced, following best practices.

> **Prerequisites**: Familiarity with the Duktig microservice structure. See [Middleware Documentation](../development/middleware.md) for conceptual overview.

## Table of Contents

1. [Overview](#overview)
2. [Understanding Middleware Basics](#understanding-middleware-basics)
3. [Project Setup](#project-setup)
4. [Creating Your First Middleware](#creating-your-first-middleware)
5. [Authorization Middleware](#authorization-middleware)
6. [Data Injection Middleware](#data-injection-middleware)
7. [Validation Middleware](#validation-middleware)
8. [Chaining Multiple Middleware](#chaining-multiple-middleware)
9. [Testing Middleware](#testing-middleware)
10. [Best Practices](#best-practices)

---

## Overview

In this tutorial, we'll create a complete API with multiple middleware layers. The example will be a **Notes API** that demonstrates:

- **Authentication** - Verify user credentials
- **Authorization** - Check user permissions
- **Data Injection** - Enrich requests with user data
- **Validation** - Validate request format before controller processes
- **Middleware Chaining** - Execute multiple middleware in sequence

### What You'll Learn

- How to structure middleware classes
- How to implement common middleware patterns
- How to configure middleware in routes
- How to debug middleware execution
- How to handle middleware errors

---

## Understanding Middleware Basics

### Middleware Execution Order

```
Client Request
    ↓
Route Match
    ↓
Middleware 1 (Authentication) - Check if user is logged in
    ↓
Middleware 2 (Authorization) - Check if user has permission
    ↓
Middleware 3 (Data Enrichment) - Inject user data
    ↓
Middleware 4 (Validation) - Validate request data
    ↓
Controller Execution
    ↓
Response to Client
```

### Key Principles

1. **Return Type**: Always return `$middlewareData` (array) or `false` (to stop)
2. **Don't Block Unnecessarily**: Only use `return false` when you need to reject the request
3. **Inject Data**: Add useful data to `$middlewareData` for the controller
4. **Early Exit**: Use `$response->sendFinal()` after sending a response
5. **Reusability**: Design middleware to be generic and reusable

---

## Project Setup

### Step 1: Create the Directory Structure

For this tutorial, we'll create middleware in:

```
src/app/Middleware/
  └── Tutorial/
      ├── Auth/
      │   ├── AuthenticateUser.php
      │   └── AuthorizeUser.php
      ├── Validation/
      │   └── ValidateNoteData.php
      └── Enrichment/
          └── InjectUserInfo.php
```

Create the directories:

```bash
mkdir -p src/app/Middleware/Tutorial/Auth
mkdir -p src/app/Middleware/Tutorial/Validation
mkdir -p src/app/Middleware/Tutorial/Enrichment
```

### Step 2: Create a Simple Model and Controller (Reference)

We'll use a simple Notes API for examples. Create `src/app/Controllers/Tutorial/NotesController.php`:

```php
<?php
namespace App\Controllers\Tutorial;

use System\HTTP\Request;
use System\HTTP\Response;

class NotesController {
    
    public function create(Request $request, Response $response, array $middlewareData): bool {
        // At this point, middleware has already:
        // - Authenticated the user
        // - Authorized their access
        // - Validated the request data
        // - Injected user information
        
        $response->sendJson([
            'status' => 'success',
            'message' => 'Note created',
            'data' => [
                'userId' => $middlewareData['user']['id'],
                'userName' => $middlewareData['user']['name'],
                'note' => $request->input('content')
            ]
        ], 201);
        
        return true;
    }
}
```

---

## Creating Your First Middleware

### Step 1: Create a Simple Logging Middleware

This is the simplest type of middleware - it just logs and passes through.

**File**: `src/app/Middleware/Tutorial/Auth/LogRequest.php`

```php
<?php
/**
 * Simple Request Logging Middleware
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Middleware\Tutorial\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

/**
 * Class LogRequest
 *
 * Logs incoming requests for debugging and monitoring
 *
 * @package App\Middleware\Tutorial\Auth
 */
class LogRequest {
    
    /**
     * Log the incoming request
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array
     */
    public function log(Request $request, Response $response, array $middlewareData): array {
        
        // Get request details
        $method = $request->method();
        $uri = $request->uri();
        $timestamp = date('Y-m-d H:i:s');
        
        // Log to file or system logger
        error_log("[$timestamp] $method $uri");
        
        // Always continue to next middleware/controller
        return $middlewareData;
    }
}
```

### Step 2: Configure in Routes

Update `src/app/config/http-routes.php`:

```php
'POST' => [
    '/api/tutorial/notes' => [
        'middleware' => [
            'Tutorial\Auth\LogRequest->log'  // This runs first
        ],
        'controller' => 'Tutorial\NotesController->create'
    ]
]
```

### Step 3: How It Works

1. Request comes in
2. LogRequest middleware logs the request details
3. Returns `$middlewareData` unchanged (continues the chain)
4. Controller processes the request
5. Response is sent to client

---

## Authorization Middleware

### Step 1: Create Authentication Middleware

This middleware checks if a user is logged in (has a valid token or API key).

**File**: `src/app/Middleware/Tutorial/Auth/AuthenticateUser.php`

```php
<?php
/**
 * User Authentication Middleware
 *
 * Verifies that the request includes valid authentication credentials
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Middleware\Tutorial\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

/**
 * Class AuthenticateUser
 *
 * @package App\Middleware\Tutorial\Auth
 */
class AuthenticateUser {
    
    /**
     * Authenticate user by API key or token
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     */
    public function check(Request $request, Response $response, array $middlewareData): array|bool {
        
        // Get API key from request header
        $apiKey = $request->headers('X-API-Key');
        
        // Check if API key is provided
        if(empty($apiKey)) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Missing authentication credentials',
                'code' => 'AUTH_MISSING'
            ], 401);
            
            $response->sendFinal();
            return false;
        }
        
        // Validate API key format (simple example)
        if(strlen($apiKey) < 32) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid API key format',
                'code' => 'AUTH_INVALID'
            ], 401);
            
            $response->sendFinal();
            return false;
        }
        
        // Verify API key against database or config (simplified example)
        $validApiKey = Config::get()['Auth']['ApiKey'] ?? null;
        
        if($apiKey !== $validApiKey) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Unauthorized API key',
                'code' => 'AUTH_UNAUTHORIZED'
            ], 401);
            
            $response->sendFinal();
            return false;
        }
        
        // Authentication passed - continue to next middleware
        return $middlewareData;
    }
}
```

### Step 2: Create Authorization Middleware

This middleware checks if the authenticated user has permission to perform the action.

**File**: `src/app/Middleware/Tutorial/Auth/AuthorizeUser.php`

```php
<?php
/**
 * User Authorization Middleware
 *
 * Verifies that the authenticated user has permission to access the resource
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Middleware\Tutorial\Auth;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class AuthorizeUser
 *
 * @package App\Middleware\Tutorial\Auth
 */
class AuthorizeUser {
    
    /**
     * Authorize user based on required role
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     */
    public function checkPermission(Request $request, Response $response, array $middlewareData): array|bool {
        
        // User info should already be injected by a previous middleware
        if(empty($middlewareData['user'])) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'User information not available',
                'code' => 'USER_NOT_FOUND'
            ], 400);
            
            $response->sendFinal();
            return false;
        }
        
        $user = $middlewareData['user'];
        
        // Check user role
        // In real app, get required role from route config or database
        $requiredRole = 'user';  // Default required role
        
        if(!isset($user['role'])) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'User role not set',
                'code' => 'ROLE_NOT_FOUND'
            ], 400);
            
            $response->sendFinal();
            return false;
        }
        
        // Check if user has required role or higher
        $roleHierarchy = ['admin' => 3, 'moderator' => 2, 'user' => 1];
        
        $userRoleLevel = $roleHierarchy[$user['role']] ?? 0;
        $requiredRoleLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        if($userRoleLevel < $requiredRoleLevel) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Insufficient permissions',
                'code' => 'UNAUTHORIZED',
                'userRole' => $user['role'],
                'requiredRole' => $requiredRole
            ], 403);
            
            $response->sendFinal();
            return false;
        }
        
        // Authorization passed - continue
        return $middlewareData;
    }
}
```

---

## Data Injection Middleware

### Create User Information Injection Middleware

This middleware retrieves user information and injects it into the request for the controller to use.

**File**: `src/app/Middleware/Tutorial/Enrichment/InjectUserInfo.php`

```php
<?php
/**
 * User Information Injection Middleware
 *
 * Retrieves user data from database and injects into middleware data
 * Should run after authentication middleware
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Middleware\Tutorial\Enrichment;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class InjectUserInfo
 *
 * @package App\Middleware\Tutorial\Enrichment
 */
class InjectUserInfo {
    
    /**
     * Inject user information from API key or token
     *
     * In a real application, this would:
     * 1. Extract user ID from the API key/token
     * 2. Query the database for user information
     * 3. Add user data to $middlewareData
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array
     */
    public function inject(Request $request, Response $response, array $middlewareData): array {
        
        // Get API key from header (authentication middleware verified it exists)
        $apiKey = $request->headers('X-API-Key');
        
        // In real app: Look up user ID from API key in database
        // For this tutorial, we'll use a simple mapping
        $usersByApiKey = [
            'test_key_12345678901234567890ab' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'admin'
            ],
            'test_key_abcdef1234567890abcdef12' => [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'role' => 'user'
            ]
        ];
        
        // Get user information
        if(isset($usersByApiKey[$apiKey])) {
            $middlewareData['user'] = $usersByApiKey[$apiKey];
        } else {
            // Set a default user (shouldn't happen if auth middleware works)
            $middlewareData['user'] = [
                'id' => 0,
                'name' => 'Unknown',
                'email' => 'unknown@example.com',
                'role' => 'guest'
            ];
        }
        
        // Add request context
        $middlewareData['requestTime'] = date('Y-m-d H:i:s');
        $middlewareData['requestIp'] = $request->ip();
        
        // Continue to next middleware/controller with enriched data
        return $middlewareData;
    }
}
```

---

## Validation Middleware

### Create Request Validation Middleware

This middleware validates the request data before it reaches the controller.

**File**: `src/app/Middleware/Tutorial/Validation/ValidateNoteData.php`

```php
<?php
/**
 * Request Data Validation Middleware
 *
 * Validates incoming request data and stops processing if invalid
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Middleware\Tutorial\Validation;

use System\HTTP\Request;
use System\HTTP\Response;
use Lib\Validator;

/**
 * Class ValidateNoteData
 *
 * @package App\Middleware\Tutorial\Validation
 */
class ValidateNoteData {
    
    /**
     * Validate POST request data for creating a note
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     */
    public function validateCreate(Request $request, Response $response, array $middlewareData): array|bool {
        
        // Define validation rules
        $validationRules = [
            'title' => 'required|string_length:3:200',
            'content' => 'required|string_length:10:5000',
            'category' => 'string_length:2:50:!required',
            'tags' => 'array:!required'
        ];
        
        // Validate request input
        $validation = Validator::validateJson(
            $request->rawInput(),
            $validationRules
        );
        
        // If validation fails, send error response and stop
        if(!empty($validation)) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Validation failed',
                'code' => 'VALIDATION_ERROR',
                'errors' => $validation
            ], 422);
            
            $response->sendFinal();
            return false;
        }
        
        // Validation passed - add validated data to middleware
        $middlewareData['validatedData'] = [
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category' => $request->input('category') ?? 'general',
            'tags' => $request->input('tags') ?? []
        ];
        
        // Continue to controller with validated data
        return $middlewareData;
    }
    
    /**
     * Validate request data for updating a note
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     */
    public function validateUpdate(Request $request, Response $response, array $middlewareData): array|bool {
        
        // For PATCH request, all fields are optional (but at least one must be provided)
        $validationRules = [
            'title' => 'string_length:3:200:!required',
            'content' => 'string_length:10:5000:!required',
            'category' => 'string_length:2:50:!required',
            'tags' => 'array:!required'
        ];
        
        $validation = Validator::validateJson(
            $request->rawInput(),
            $validationRules
        );
        
        if(!empty($validation)) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Validation failed',
                'code' => 'VALIDATION_ERROR',
                'errors' => $validation
            ], 422);
            
            $response->sendFinal();
            return false;
        }
        
        // Check that at least one field is provided
        $input = json_decode($request->rawInput(), true) ?? [];
        
        if(empty($input)) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'No fields to update provided',
                'code' => 'EMPTY_UPDATE'
            ], 422);
            
            $response->sendFinal();
            return false;
        }
        
        // Continue with update data
        $middlewareData['validatedData'] = $input;
        
        return $middlewareData;
    }
}
```

---

## Chaining Multiple Middleware

### Step 1: Configure Multiple Middleware in Routes

**File**: `src/app/config/http-routes.php`

```php
'POST' => [
    # ... existing routes ...
    
    '/api/tutorial/notes' => [
        # Multiple middleware execute in order
        'middleware' => [
            'Tutorial\Auth\LogRequest->log',              # 1. Log the request
            'Tutorial\Auth\AuthenticateUser->check',      # 2. Verify API key
            'Tutorial\Enrichment\InjectUserInfo->inject', # 3. Get user info
            'Tutorial\Auth\AuthorizeUser->checkPermission', # 4. Check permissions
            'Tutorial\Validation\ValidateNoteData->validateCreate' # 5. Validate data
        ],
        'controller' => 'Tutorial\NotesController->create'
    ],
    
    '/api/tutorial/notes/{id}' => [
        'middleware' => [
            'Tutorial\Auth\AuthenticateUser->check',
            'Tutorial\Enrichment\InjectUserInfo->inject'
        ],
        'controller' => 'Tutorial\NotesController->get'
    ]
],
'PATCH' => [
    '/api/tutorial/notes/{id}' => [
        'middleware' => [
            'Tutorial\Auth\AuthenticateUser->check',
            'Tutorial\Enrichment\InjectUserInfo->inject',
            'Tutorial\Auth\AuthorizeUser->checkPermission',
            'Tutorial\Validation\ValidateNoteData->validateUpdate'
        ],
        'controller' => 'Tutorial\NotesController->update'
    ]
]
```

### Step 2: Understand the Execution Flow

When a request comes to `/api/tutorial/notes`:

```
1. LogRequest->log()
   ├─ Logs: POST /api/tutorial/notes
   └─ Returns: $middlewareData unchanged
   
2. AuthenticateUser->check()
   ├─ Reads: X-API-Key header
   ├─ Validates: Key format and existence
   └─ Returns: $middlewareData unchanged (if valid)
   
3. InjectUserInfo->inject()
   ├─ Looks up user by API key
   ├─ Adds: $middlewareData['user']
   └─ Returns: Enriched $middlewareData
   
4. AuthorizeUser->checkPermission()
   ├─ Reads: $middlewareData['user']
   ├─ Checks: User role/permissions
   └─ Returns: $middlewareData unchanged (if authorized)
   
5. ValidateNoteData->validateCreate()
   ├─ Validates: title, content, category, tags
   ├─ Adds: $middlewareData['validatedData']
   └─ Returns: $middlewareData with validated data
   
6. NotesController->create()
   ├─ Receives: $middlewareData with user, validatedData
   ├─ Processes: Creates note in database
   └─ Returns: JSON response
```

---

## Testing Middleware

### Step 1: Test Individual Middleware

Create a simple test endpoint for debugging:

```php
# In http-routes.php
'GET' => [
    '/api/tutorial/test-middleware' => [
        'middleware' => [
            'Tutorial\Auth\LogRequest->log',
            'Tutorial\Auth\AuthenticateUser->check',
            'Tutorial\Enrichment\InjectUserInfo->inject'
        ],
        'controller' => 'Tutorial\NotesController->testMiddleware'
    ]
]
```

```php
# In NotesController.php
public function testMiddleware(Request $request, Response $response, array $middlewareData): bool {
    
    // Return the middleware data to see what was injected
    $response->sendJson([
        'status' => 'success',
        'message' => 'Middleware data received',
        'data' => $middlewareData
    ], 200);
    
    return true;
}
```

### Step 2: Test with cURL

```bash
# Test without API key (should fail at authentication)
curl -X GET http://localhost/api/tutorial/test-middleware

# Test with valid API key
curl -X GET http://localhost/api/tutorial/test-middleware \
  -H "X-API-Key: test_key_12345678901234567890ab"

# Test creating a note
curl -X POST http://localhost/api/tutorial/notes \
  -H "X-API-Key: test_key_12345678901234567890ab" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Note",
    "content": "This is a test note with some meaningful content",
    "category": "personal",
    "tags": ["test", "tutorial"]
  }'
```

### Step 3: Debug Middleware Execution

Add logging to track middleware execution:

```php
# In each middleware method
public function check(Request $request, Response $response, array $middlewareData): array|bool {
    
    // Log execution
    error_log('[MIDDLEWARE] ' . __CLASS__ . '->' . __FUNCTION__ . ' START');
    
    // ... your code ...
    
    error_log('[MIDDLEWARE] ' . __CLASS__ . '->' . __FUNCTION__ . ' COMPLETE');
    
    return $middlewareData;
}
```

Check the PHP error log to track execution order:

```bash
tail -f /path/to/php/error.log | grep MIDDLEWARE
```

---

## Best Practices

### 1. Keep Middleware Focused

**Good**: Authentication middleware only authenticates

```php
public function check(Request $request, Response $response, array $middlewareData): array|bool {
    $apiKey = $request->headers('X-API-Key');
    if($apiKey === Config::get()['Auth']['ApiKey']) {
        return $middlewareData;
    }
    // Return error
}
```

**Bad**: Authentication middleware doing too much

```php
public function check(Request $request, Response $response, array $middlewareData): array|bool {
    // Authenticating
    // Authorizing
    // Validating
    // Injecting user data
    // Logging
    // Caching
}
```

### 2. Use Descriptive Names

**Good**: `AuthenticateUser`, `ValidateNoteData`, `InjectUserInfo`

**Bad**: `Check`, `Process`, `Middleware1`

### 3. Return Early on Errors

**Good**: Stop immediately if validation fails

```php
if($validation) {
    $response->sendJson($validation, 422);
    $response->sendFinal();
    return false;
}
```

**Bad**: Continue despite errors

```php
if($validation) {
    // Keep going anyway
}
```

### 4. Add Comprehensive Comments

```php
/**
 * Validate user API key
 *
 * This middleware checks if the request includes a valid API key.
 * It should be placed first in the middleware chain.
 *
 * Expected Header: X-API-Key
 * Validation: Format check and database lookup
 *
 * Returns:
 * - array: If valid, passes control to next middleware
 * - false: If invalid, sends 401 response and stops
 *
 * @param Request $request
 * @param Response $response
 * @param array $middlewareData
 * @return array|bool
 */
public function check(Request $request, Response $response, array $middlewareData): array|bool {
```

### 5. Handle Errors Gracefully

**Good**: Return specific error codes and messages

```php
$response->sendJson([
    'status' => 'error',
    'message' => 'Missing authentication credentials',
    'code' => 'AUTH_MISSING'
], 401);
```

**Bad**: Generic errors

```php
$response->sendJson(['error' => 'Failed']);
```

### 6. Don't Repeat Middleware Logic

**Good**: Create reusable middleware for common patterns

**Bad**: Copy-paste similar code across routes

### 7. Order Matters

```php
'middleware' => [
    'Auth->authenticate',      # First: Verify identity
    'EnrichData->inject',      # Second: Add user info
    'Auth->authorize',         # Third: Check permissions
    'Validate->data'           # Fourth: Validate input
]
```

### 8. Test Edge Cases

- Missing headers
- Invalid data formats
- Empty arrays
- Null values
- Expired credentials

```php
# Test file: tests/Unit/MiddlewareTest.php
public function testMissingApiKey() {
    $request = new Request();
    $response = new Response();
    
    $middleware = new AuthenticateUser();
    $result = $middleware->check($request, $response, []);
    
    $this->assertFalse($result);
    $this->assertEquals(401, $response->getStatusCode());
}
```

---

## Common Middleware Patterns

### Pattern 1: Authentication & Authorization

```php
'middleware' => [
    'Auth->authenticate',    # Check if user exists
    'Data->injectUser',      # Add user details
    'Auth->authorize'        # Check permissions
]
```

### Pattern 2: Validation

```php
'middleware' => [
    'Auth->authenticate',
    'Validate->request'      # Validate input data
]
```

### Pattern 3: Data Enrichment

```php
'middleware' => [
    'Auth->authenticate',
    'Data->injectUser',      # Add user info
    'Data->injectContext'    # Add request context
]
```

### Pattern 4: Caching

```php
'middleware' => [
    'Cache->responseFromCache',  # Try to get cached response
    // If no cache, continue to controller
    'Auth->authenticate'
]
```

---

## Troubleshooting

### Issue: Middleware not executing

**Check**:
1. Middleware class path correct?
2. Namespace matches directory structure?
3. Method name spelled correctly?
4. Middleware properly configured in routes?

### Issue: Data not reaching controller

**Check**:
1. Middleware returns `$middlewareData` (not `true`)?
2. Using correct key names?
3. Check middleware execution order?

### Issue: Request rejected unexpectedly

**Check**:
1. Authentication credentials provided?
2. User has required permissions?
3. Validation rules correct?
4. Check error response message?

### Issue: Middleware executing in wrong order

**Check**:
1. Route middleware configuration array order?
2. Dependencies between middleware?
3. Early exit with `sendFinal()`?

---

## Summary

You now understand:

✓ How to create basic middleware
✓ Authentication and authorization patterns
✓ Data injection techniques
✓ Validation middleware
✓ Middleware chaining
✓ Testing and debugging
✓ Best practices

## Next Steps

1. Review the [Middleware Documentation](../development/middleware.md) for detailed concepts
2. Examine existing middleware in `/src/app/Middleware/` for patterns
3. Create your own middleware for your API
4. Implement error handling and logging
5. Write unit tests for your middleware

For more complex scenarios, check the built-in middleware examples in the framework for reference implementations.
