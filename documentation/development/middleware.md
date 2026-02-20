# Duktig PHP Microservice - Development documentation

## Middleware Documentation

**Table of Contents**

1. [Why Middleware is Needed](#why-middleware-is-needed)
2. [How Middleware Works](#how-middleware-works)
3. [Middleware Examples](#middleware-examples)
4. [Creating Middleware](#creating-middleware)

---

## Why Middleware is Needed

Middleware serves as a bridge between incoming HTTP/CLI requests and your application's controllers. It allows you to execute logic **before** a controller processes the request and **before** a response is sent to the client.

### Key Benefits

- **Authentication & Authorization**: Validate user credentials and permissions before allowing access to protected resources
- **Request Validation**: Check and validate incoming request data before it reaches the controller
- **Data Injection**: Enrich the request with additional data (user info, account details, etc.)
- **Response Caching**: Return cached responses instead of querying the database every time
- **Request Logging**: Log important information about incoming requests
- **CORS & Security Headers**: Add security headers to responses
- **Rate Limiting**: Control the number of requests from a client
- **Data Transformation**: Transform request/response data into the required format

### When to Use Middleware

Use middleware when you need to perform an action that:
- Applies to **multiple routes** (authentication, logging)
- Should **block** a request before it reaches the controller
- Needs to **enrich** the request with additional data
- Should be **reusable** across different endpoints

---

## How Middleware Works

### Middleware Flow

```
HTTP/CLI Request
    ↓
Route Matching
    ↓
Middleware Chain Execution (in order)
    ├─ Middleware 1: Check conditions
    ├─ Middleware 2: Validate data
    └─ Middleware 3: Inject data
    ↓
Controller Execution
    ↓
Response sent to Client
```

### Middleware Structure

Every middleware class must contain a method that accepts three parameters:

```php
public function methodName(
    Request $request,           // The incoming request object
    Response $response,         // The response object (to send errors)
    array $middlewareData       // Data shared between middleware and controller
)
```

### Middleware Data Flow

1. **Input**: `$middlewareData` array contains data from previous middleware
2. **Processing**: Your middleware checks conditions or modifies data
3. **Output**: 
   - **Return `$middlewareData`** (array): Continue to next middleware/controller
   - **Return `false`**: Stop processing and send response to client
   - **Return modified array**: Pass enhanced data to next middleware/controller

### Middleware Configuration

Middleware is configured in route files (`http-routes.php` or `cli-routes.php`):

```php
'middleware' => [
    'ClassName->methodName',
    'AnotherClass->anotherMethod'
]
```

The format is: **Namespace\ClassName->methodName**

Middleware executes in the order specified in the array.

---

## Middleware Examples

### Example 1: Authorization Middleware

**File**: `/src/app/Middleware/General/Auth/AuthByKey.php`

This middleware checks if a request has a valid API key:

```php
<?php
namespace App\Middleware\General\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

class AuthByKey {
    
    public function check(Request $request, Response $response, array $middlewareData) {
        
        # Get Auth config
        $config = Config::get()['Auth'];
        
        # Get Auth key from Headers
        $authKey = $request->headers($config['AuthKey']);
        
        # Validate the key
        if($authKey == $config['AuthKeyValue']) {
            return $middlewareData;  // Continue to controller
        }
        
        # Unauthorized - send error response
        $response->sendJson([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
        
        $response->sendFinal();  // Stop further processing
        return false;
    }
}
```

**Usage in routes**:
```php
'GET' => [
    '/api/protected-data' => [
        'middleware' => [
            'General\Auth\AuthByKey->check'
        ],
        'controller' => 'YourController->getData'
    ]
]
```

**How it works**:
1. Checks if request header contains valid API key
2. If valid → allows request to proceed to controller
3. If invalid → sends 401 error and stops processing

---

### Example 2: Data Injection Middleware

**File**: `/src/app/Middleware/Development/Examples/Injection.php`

This middleware adds data to the request without blocking it:

```php
<?php
namespace App\Middleware\Development\Examples;

use System\HTTP\Request;
use System\HTTP\Response;

class Injection {
    
    public function injectMiddlewareData(Request $request, Response $response, array $middlewareData): array {
        
        # Add request statistics
        $middlewareData['GET_Request_count'] = count($request->get());
        
        # Add custom data
        $middlewareData['Some_other_data'] = 'This is a message injected in Middleware.';
        
        # Return enhanced data to controller
        return $middlewareData;
    }
}
```

**How it works**:
1. Receives `$middlewareData` from previous middleware
2. Adds new key-value pairs to the array
3. Returns the enhanced array to next middleware or controller
4. Never stops the request flow

---

### Example 3: Response Caching Middleware

**File**: `/src/app/Middleware/Development/Examples/DataCaching.php`

This middleware returns cached responses to avoid database queries:

```php
<?php
namespace App\Middleware\Development\Examples;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;
use Lib\Cache\Redis as CacheClient;

class DataCaching {
    
    public function responseFromCache(Request $request, Response $response, array $middlewareData): array {
        
        # Initialize cache client
        $cacheLib = new CacheClient(Config::get()['Redis']['SystemCaching']);
        
        # Generate cache key from request URI
        $key = md5($request->uri());
        
        # Try to get cached data
        $content = $cacheLib->getArray($key);
        
        # If cache exists
        if(!empty($content)) {
            # Add cache metadata
            $content['type'] = 'Cached';
            $content['message'] = 'This data comes from cache.';
            
            # Send response immediately
            $response->sendJson($content);
            $response->sendFinal();
        }
        
        # If no cache, continue to controller
        return $middlewareData;
    }
}
```

**How it works**:
1. Checks if response is cached using Redis
2. If found → sends cached response and stops processing (returns nothing)
3. If not found → allows controller to execute and generate response

---

### Example 4: Data Injection from Headers

**File**: `/src/app/Middleware/Accounts/Account/InjectHeaderAccountInfo.php`

This middleware extracts and injects account information from headers:

```php
<?php
namespace App\Middleware\Accounts\Account;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

class InjectHeaderAccountInfo {
    
    public function injectFromHeaders(Request $request, Response $response, array $middlewareData): false|array {
        
        # Get account info from custom header
        $data = json_decode($request->headers('X-Account-Info'), true);
        
        # Validate the header data
        if(!$data) {
            # Send error response
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid header data'
            ], 400);
            
            $response->sendFinal();
            return false;  // Stop processing
        }
        
        # Inject account data
        $middlewareData['account'] = $data;
        
        return $middlewareData;  // Continue with injected data
    }
}
```

**How it works**:
1. Extracts account information from `X-Account-Info` header
2. If invalid → sends error and stops
3. If valid → injects into middleware data and continues

---

## Creating Middleware

### File Structure

Middleware classes are located in: `/src/app/Middleware/{Category}/{Type}/`

Examples:
- `/src/app/Middleware/General/Auth/AuthByKey.php`
- `/src/app/Middleware/Accounts/Account/VerifyAccountDbStatus.php`
- `/src/app/Middleware/Development/Examples/DataCaching.php`

### Basic Middleware Template

```php
<?php
/**
 * Brief description of what this middleware does
 *
 * @author Your Name <your-email@example.com>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\YourCategory\YourType;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class YourMiddlewareName
 *
 * @package App\Middleware
 */
class YourMiddlewareName {
    
    /**
     * Your middleware method
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return array|bool
     */
    public function yourMethodName(Request $request, Response $response, array $middlewareData) {
        
        // Your logic here
        
        // If successful, return the data (possibly modified)
        return $middlewareData;
        
        // If you need to stop processing and send an error:
        // $response->sendJson(['error' => 'message'], 400);
        // $response->sendFinal();
        // return false;
    }
}
```

---

## Important Notes

- **Middleware Order Matters**: Middleware executes in the order specified in the config array
- **Early Exit**: Use `$response->sendFinal()` to stop further processing
- **Data Persistence**: Data injected in middleware is available in the controller via the middleware data parameter
- **No Direct Output**: Never use `echo` or `print` in middleware; always use `$response->sendJson()` or similar
- **Exception Handling**: Consider using try-catch blocks for robust error handling
- **Performance**: Keep middleware logic minimal; avoid heavy operations that impact request performance

---

## Next Steps

For a practical tutorial on creating middleware, see the **Middleware Tutorial** section in the documentation.
