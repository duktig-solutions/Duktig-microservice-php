# Duktig PHP Microservice - Development documentation

## HTTP Workflow

The **HTTP Workflow** in Duktig PHP Microservice defines the full lifecycle of every HTTP request — from the moment it enters the system to the moment the response is sent back to the client.

This flow includes initialization of core HTTP objects, routing lookup, middleware execution, controller execution, and final response handling.

---

### Entry Point

The HTTP processing begins in:

`./www/index.php`

This file is responsible for bootstrapping the microservice environment and preparing the HTTP workflow.

**Main steps:**

1. Load system configurations and autoloader.
2. Initialize `Request` and `Response` class objects.
3. Execute routing using configuration file:  
   `./app/config/http-routes.php`
4. If the route exists, execute defined middleware(s) sequentially.
5. Execute the controller method for the route.
6. Send the final HTTP response back to the client.

---

### Initialization

#### Request

The `Request` class provides access to all HTTP request data:

- HTTP method (GET, POST, PUT, PATCH, DELETE, etc.)
- Path and parameters
- Query, JSON, or form data
- Headers and cookies

Example:

```php
$request = new \Lib\Http\Request();

$method = $request->method();  // e.g. GET
$uri = $request->uri();        // e.g. /examples/get-file
$data = $request->input();     // payload data
```

#### Response

The `Response` class is used to build and send output data back to the client.

Example:

```php
$response = new \Lib\Http\Response();

$response->json(['status' => 'ok']);
$response->send();
```

Both `Request` and `Response` objects are passed throughout the entire workflow — including all middleware and controllers.

---

### Routing

The routing configuration is located in:

`./app/config/http-routes.php`

This file maps HTTP methods and paths to specific controller methods, along with optional middleware and caching definitions.

Example route:

```php
'GET' => [
    '/examples/get-file' => [
        'middleware' => [
            'Development\Auth\AuthByDeveloperKey->check'
        ],
        'controller' => 'Development\Examples\Getter->downloadFile'
    ]
]
```

#### Route Matching Logic

1. Identify HTTP method (GET, POST, etc.).
2. Match the path pattern against defined routes.
3. If no route is found, respond with `404 Not Found`.
4. If a match is found, extract:
    - Middleware list
    - Controller
    - Optional `permissionsRequired`
    - Optional `cacheConfig`

---

### Middleware Execution

If a route defines one or more middleware, they are executed **sequentially** before the controller is called.

Each middleware method receives **three parameters**:

```php
middlewareFunction($request, $response, $middlewareData)
```

#### Parameters:

- `$request` — The current `Request` object.
- `$response` — The current `Response` object.
- `$middlewareData` — Shared data array passed between all middleware and the controller.

---

#### Shared `$middlewareData`

The `$middlewareData` array is an important part of the HTTP workflow.  
It allows middleware to **inject or modify data** that will later be accessible inside the controller.

Example:

```php
class ExampleMiddleware {
    public function authCheck($request, $response, $middlewareData) {
        // Perform some authentication
        $userId = 123;

        // Inject data for later use
        $middlewareData['authenticated_user'] = $userId;

        return $middlewareData; // Return the updated array
    }
}
```

This updated `$middlewareData` will then be available to the next middleware and finally to the controller.

---

### Controller Execution

After all middleware are successfully executed, the controller is called.

Each controller method receives the following parameters:

```php
myControllerMethod($request, $response, $middlewareData)
```

#### Parameters:

- `$request` — The HTTP request object.
- `$response` — The HTTP response object.
- `$middlewareData` — The array containing all data passed or modified by middleware.

Example:

```php
class UserController {

    public function getProfile($request, $response, $middlewareData) {

        // Access injected data from middleware
        $userId = $middlewareData['authenticated_user'] ?? null;

        if(!$userId) {
            return $response->json(['error' => 'Not authorized'], 403);
        }

        // Load user data
        $user = $this->model->find($userId);

        return $response->json(['user' => $user]);
    }
}
```

---

### Middleware Flow Example

```php
'middleware' => [
    'Auth\UserAuth->checkToken',
    'Cache\SystemCache->readFromCache'
],
'controller' => 'App\Controllers\UserController->getProfile'
```

Execution order:

1. `UserAuth->checkToken()`
    - Verifies authentication
    - Adds `$middlewareData['user_id'] = 123`

2. `SystemCache->readFromCache()`
    - Checks cache
    - Optionally modifies `$middlewareData['cache_hit']`

3. `UserController->getProfile()`
    - Accesses `$middlewareData['user_id']` and `$middlewareData['cache_hit']`
    - Returns response

---

### Middleware Can Stop the Flow

A middleware may send a response directly and **terminate the workflow**.  
If this happens, no further middleware or controller will run.

Example:

```php
public function checkToken($request, $response, $middlewareData) {

    if(!$request->header('Authorization')) {
        $response->json(['error' => 'Missing token'], 401);
        return false; // Stop further execution
    }

    return $middlewareData;
}
```

---

### Example Workflow Summary

1. **Entry:**  
   `./www/index.php` initializes `Request`, `Response`, and starts routing.

2. **Routing:**  
   Looks up the route in `./app/config/http-routes.php`.

3. **Middleware chain:**  
   Executes each middleware in order, passing and updating `$middlewareData`.

4. **Controller execution:**  
   Controller receives `$request`, `$response`, `$middlewareData`.

5. **Response:**  
   Controller (or middleware) uses `Response` to send data to client.

---

### Example Workflow Diagram

```
Client Request
      │
      ▼
 ./www/index.php
      │
      ▼
Initialize Request + Response
      │
      ▼
Routing (./app/config/http-routes.php)
      │
      ├── Route not found → 404 Response
      │
      └── Route found
              │
              ▼
       Middleware(s)
       ├── Auth Middleware → adds user_id to $middlewareData
       ├── Cache Middleware → reads cache
       └── Validation Middleware
              │
              ▼
         Controller
         │ Receives ($request, $response, $middlewareData)
              │
              ▼
          Response
              │
              ▼
        Output to Client
```

---

End of document