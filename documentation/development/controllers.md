# Duktig PHP Microservice - Development documentation

## Controllers

**Table of Contents**

1. [What Are Controllers](#what-are-controllers)
2. [Controller Structure](#controller-structure)
3. [HTTP Controllers](#http-controllers)
4. [CLI Controllers](#cli-controllers)
5. [Creating a Controller](#creating-a-controller)
6. [Best Practices](#best-practices)
7. [Common Patterns](#common-patterns)

---

## What Are Controllers

Controllers are the core business logic components in the Duktig PHP Microservice framework. They handle incoming requests, process data, and send responses back to clients.

Controllers serve as the entry point after routing and middleware execution. They are responsible for:

- Receiving and processing incoming request data
- Validating input
- Interacting with models and services
- Orchestrating business logic
- Returning appropriate responses

### Controller Types

The framework supports two types of controllers:

1. **HTTP Controllers** - Handle REST API requests (GET, POST, PUT, PATCH, DELETE)
2. **CLI Controllers** - Handle command-line requests for background jobs and automation

---

## Controller Structure

### File Organization

Controllers are organized hierarchically in the following directory:

```
src/app/Controllers/
├── Accounts/
│   ├── Account/
│   ├── Profiles/
│   └── AccountsManagement/
├── Development/
│   └── Examples/
├── System/
│   ├── HealthCheck/
│   ├── Logs/
│   └── Backups/
└── Auth/
```

### Namespace Convention

Each controller must follow the namespace convention:

```php
namespace App\Controllers\{Category}\{SubCategory};
```

Examples:

- `App\Controllers\Accounts\Account\Signin`
- `App\Controllers\Development\Examples\Getter`
- `App\Controllers\System\HealthCheck\SystemHealthCheck`

### Class Structure

Every controller class should follow this basic structure:

```php
<?php
/**
 * Controller Description
 *
 * Brief description of what this controller does.
 * 
 * @author Your Name <your@email.com>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\YourCategory\YourSubcategory;

use System\HTTP\Request;
use System\HTTP\Response;
use System\CLI\Input;
use System\CLI\Output;
// ... other imports

/**
 * Class ControllerName
 *
 * Detailed description of controller functionality
 *
 * @package App\Controllers\YourCategory\YourSubcategory
 */
class ControllerName {

    /**
     * Controller Method
     *
     * Detailed method description
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool|void
     */
    public function methodName(Request $request, Response $response, array $middlewareData) {
        // Controller logic here
    }
}
```

---

## HTTP Controllers

### Method Signature

HTTP controller methods must accept exactly three parameters and return a boolean or void:

```php
public function methodName(
    Request $request,          // The incoming HTTP request
    Response $response,        // The HTTP response object
    array $middlewareData      // Data from middleware chain
) : bool
```

### Parameter Details

#### Request Object

The `Request` object provides access to all incoming HTTP data:

```php
$method = $request->method();              // HTTP method: GET, POST, etc.
$uri = $request->uri();                    // Request path: /api/users/123
$paths = $request->paths();                // Path segments as array
$allInput = $request->input();             // Form or JSON data
$rawInput = $request->rawInput();          // Raw POST body
$getData = $request->get();                // Query string parameters
$headers = $request->headers();            // HTTP headers
$cookie = $request->cookie('name');        // Cookie value
$server = $request->server('key');         // Server variables
```

#### Response Object

The `Response` object is used to send responses back to the client:

```php
$response->sendJson(['data' => 'value'], 200);   // JSON response
$response->sendFile('path/to/file');             // Download file
$response->setHeader('X-Custom', 'value');       // Set HTTP header
$response->setCookie('name', 'value');           // Set cookie
$response->sendFinal();                          // Force immediate response
```

#### Middleware Data

The `$middlewareData` array contains data passed from middleware components:

```php
// Middleware might inject user information
$user = $middlewareData['user'] ?? null;
$permissions = $middlewareData['permissions'] ?? [];
$cached = $middlewareData['cached_response'] ?? null;
```

### Return Values

- **Return `true`** - Request processed successfully
- **Return `false`** - Request failed (response already sent)
- **Return `void`** - No explicit return (implicit success)

### Example: HTTP Controller

```php
<?php
namespace App\Controllers\Users;

use Lib\Validator;
use System\HTTP\Request;
use System\HTTP\Response;
use App\Models\Users\User as UserModel;

class Users {

    /**
     * Get all users with pagination
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function getAll(Request $request, Response $response, array $middlewareData) : bool {
        
        $page = (int) $request->get('page') ?? 1;
        $limit = (int) $request->get('limit') ?? 10;

        try {
            $userModel = new UserModel();
            $users = $userModel->getAll($page, $limit);

            $response->sendJson([
                'status' => 'success',
                'data' => $users,
                'page' => $page,
                'limit' => $limit
            ], 200);

            return true;
        } catch (\Exception $e) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Failed to retrieve users'
            ], 500);
            return false;
        }
    }

    /**
     * Create a new user
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function create(Request $request, Response $response, array $middlewareData) : bool {
        
        // Validate input
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'email' => 'required|email',
                'name' => 'required|string',
                'password' => 'required|password:6:256'
            ]
        );

        if (!empty($validation)) {
            $response->sendJson(['errors' => $validation], 422);
            return false;
        }

        try {
            $data = $request->input();
            $userModel = new UserModel();
            $newUser = $userModel->create($data);

            $response->sendJson([
                'status' => 'success',
                'data' => $newUser
            ], 201);

            return true;
        } catch (\Exception $e) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Failed to create user'
            ], 500);
            return false;
        }
    }
}
```

---

## CLI Controllers

### Method Signature

CLI controller methods must accept exactly three parameters and can return void or any value:

```php
public function methodName(
    Input $input,              // The CLI input/arguments
    Output $output,            // The CLI output
    array $middlewareData      // Data from middleware chain
) : void
```

### Parameter Details

#### Input Object

The `Input` class parses command-line arguments:

```php
$routeName = $input->route();              // The command name
$parsed = $input->parsed('name');          // Get specific parsed argument
$allParsed = $input->parsed();             // Get all parsed arguments
$args = $input->args();                    // Get all original arguments
$input->stdin();                           // Read from STDIN for interactive input
```

Example command:

```bash
php cli/exec.php backup-database --days 30 --compress yes
```

Parsed as:

```php
$input->route();                 // "backup-database"
$input->parsed('days');          // "30"
$input->parsed('compress');      // "yes"
$input->parsed();                // ['days' => '30', 'compress' => 'yes']
```

#### Output Object

The `Output` class writes to console:

```php
$output->stdout("Normal message");     // Write to STDOUT
$output->stderr("Error occurred");     // Write to STDERR and exit
$output->usage();                      // Display usage and exit
```

### Example: CLI Controller

```php
<?php
namespace App\Controllers\System\Backups;

use System\CLI\Input;
use System\CLI\Output;
use System\Config;
use System\Logger;

class DatabaseBackup {

    /**
     * Backup database
     *
     * Usage: php cli/exec.php db-backup --database mydb --compress yes
     *
     * @param Input $input
     * @param Output $output
     * @param array $middlewareData
     * @return void
     */
    public function process(Input $input, Output $output, array $middlewareData) : void {
        
        $dbName = $input->parsed('database');
        $compress = $input->parsed('compress') === 'yes';
        
        if (!$dbName) {
            $output->stderr("Error: --database parameter is required");
            return;
        }

        try {
            $output->stdout("Starting backup for database: {$dbName}");

            // Perform backup logic here
            $backupFile = $this->performBackup($dbName, $compress);

            $output->stdout("Backup completed successfully");
            $output->stdout("Backup file: {$backupFile}");

            Logger::Log("Database backup completed: {$backupFile}", Logger::INFO);

        } catch (\Exception $e) {
            $output->stderr("Backup failed: " . $e->getMessage());
            Logger::Log("Database backup failed: " . $e->getMessage(), Logger::ERROR);
        }
    }

    private function performBackup(string $dbName, bool $compress) : string {
        // Backup implementation
        return "/backups/db/{$dbName}_backup.sql";
    }
}
```

---

## Creating a Controller

### Step 1: Choose the Right Location

Determine the category and subcategory based on functionality:

```
Controllers/
├── Accounts/       # User account management
├── Development/    # Development & testing endpoints
├── System/        # System administration
└── Auth/          # Authentication
```

### Step 2: Create the Controller File

Create the PHP file following the naming convention:

```php
<?php
/**
 * My Feature Controller
 *
 * Detailed description of what this controller does.
 *
 * @author Your Name <your@email.com>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\MyCategory\MySubcategory;

use System\HTTP\Request;
use System\HTTP\Response;

class MyController {

    public function myMethod(Request $request, Response $response, array $middlewareData) : bool {
        // Your implementation here
        return true;
    }
}
```

### Step 3: Register the Route

Add the route to the appropriate configuration file:

**For HTTP endpoints** - Edit `src/app/config/http-routes.php`:

```php
'GET' => [
    '/my-endpoint/{id}' => [
        'middleware' => [
            'Auth\Authenticate->verify'
        ],
        'controller' => 'MyCategory\MySubcategory\MyController->myMethod'
    ]
]
```

**For CLI commands** - Edit `src/app/config/cli-routes.php`:

```php
'my-command' => [
    'controller' => 'MyCategory\MySubcategory\MyController->myMethod',
    'middleware' => [],
    'executeUniqueProcessLifeTime' => 10
]
```

### Step 4: Test the Controller

For HTTP controllers, test using curl or a REST client:

```bash
curl http://localhost/my-endpoint/123
```

For CLI controllers, test from the command line:

```bash
php cli/exec.php my-command --param value
```

---

## Best Practices

### 1. Input Validation

Always validate input before processing:

```php
use Lib\Validator;

$validation = Validator::validateJson(
    $request->rawInput(),
    [
        'email' => 'required|email',
        'age' => 'required|digits|int_range:18:65'
    ]
);

if (!empty($validation)) {
    $response->sendJson(['errors' => $validation], 422);
    return false;
}
```

### 3. Use Models for Data Access

Separate business logic from data access by using models:

```php
public function getUser(Request $request, Response $response, array $middlewareData) : bool {
    
    $userId = $request->paths(0) ?? null;
    
    try {
        $userModel = new UserModel();
        $user = $userModel->findById($userId);
        
        if (!$user) {
            $response->sendJson(['error' => 'User not found'], 404);
            return false;
        }

        $response->sendJson(['data' => $user], 200);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Server error'], 500);
        return false;
    }
}
```

### 4. Logging

Log important events and errors:

```php
use System\Logger;

Logger::Log("User login successful: {$userId}", Logger::INFO);
Logger::Log("Database query failed: {$error}", Logger::ERROR);
Logger::Log("Backup completed in {$duration}ms", Logger::INFO);
```

### 5. Use Middleware for Cross-Cutting Concerns

Don't put authentication/caching logic in controllers; use middleware:

```php
// DON'T - Put authentication in controller
public function getUser(Request $request, Response $response, array $middlewareData) : bool {
    if (!$this->isUserAuthenticated()) {
        $response->sendJson(['error' => 'Unauthorized'], 401);
        return false;
    }
    // ... rest of logic
}

// DO - Use middleware
'GET' => [
    '/user/{id}' => [
        'middleware' => ['Auth\Authenticate->verify'],  // Authentication handled here
        'controller' => 'Users\Users->getUser'
    ]
]
```

### 6. Consistent Response Format

Use a consistent response format across all endpoints:

```php
// Success response
$response->sendJson([
    'status' => 'success',
    'data' => $data
], 200);

// Error response
$response->sendJson([
    'status' => 'error',
    'message' => 'Description of error'
], 400);
```

### 7. HTTP Status Codes

Use appropriate HTTP status codes:

| Code | Meaning | Usage |
|------|---------|-------|
| `200` | OK | Request successful |
| `201` | Created | New resource created |
| `400` | Bad Request | Invalid input |
| `401` | Unauthorized | Authentication required |
| `403` | Forbidden | Permission denied |
| `404` | Not Found | Resource not found |
| `422` | Unprocessable Entity | Validation error |
| `500` | Server Error | Unexpected error |

---

## Common Patterns

### Pattern 1: CRUD Operations

```php
public function create(Request $request, Response $response, array $middlewareData) : bool {
    // Validate
    $validation = Validator::validateJson($request->rawInput(), [...]);
    if (!empty($validation)) {
        $response->sendJson(['errors' => $validation], 422);
        return false;
    }

    try {
        $model = new MyModel();
        $resource = $model->create($request->input());
        $response->sendJson(['data' => $resource], 201);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Creation failed'], 500);
        return false;
    }
}

public function read(Request $request, Response $response, array $middlewareData) : bool {
    try {
        $id = $request->paths(0);
        $model = new MyModel();
        $resource = $model->findById($id);
        
        if (!$resource) {
            $response->sendJson(['error' => 'Not found'], 404);
            return false;
        }

        $response->sendJson(['data' => $resource], 200);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Read failed'], 500);
        return false;
    }
}

public function update(Request $request, Response $response, array $middlewareData) : bool {
    $id = $request->paths()['id'];
    
    $validation = Validator::validateJson($request->rawInput(), [...]);
    if (!empty($validation)) {
        $response->sendJson(['errors' => $validation], 422);
        return false;
    }

    try {
        $model = new MyModel();
        $resource = $model->update($id, $request->input());
        $response->sendJson(['data' => $resource], 200);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Update failed'], 500);
        return false;
    }
}

public function delete(Request $request, Response $response, array $middlewareData) : bool {
    try {
        $id = $request->paths()['id'];
        $model = new MyModel();
        $model->delete($id);
        $response->sendJson(['status' => 'deleted'], 200);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Delete failed'], 500);
        return false;
    }
}
```

### Pattern 2: Middleware Data Injection

```php
public function getUserProfile(Request $request, Response $response, array $middlewareData) : bool {
    
    // Authentication middleware provides user data
    $currentUser = $middlewareData['user'] ?? null;
    
    if (!$currentUser) {
        $response->sendJson(['error' => 'Unauthorized'], 401);
        return false;
    }

    try {
        $model = new UserModel();
        $profile = $model->getProfile($currentUser['id']);
        $response->sendJson(['data' => $profile], 200);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Failed to fetch profile'], 500);
        return false;
    }
}
```

### Pattern 3: Query String Filtering

```php
public function searchUsers(Request $request, Response $response, array $middlewareData) : bool {
    
    $filters = [
        'search' => $request->get('search'),
        'role' => $request->get('role'),
        'status' => $request->get('status'),
        'page' => (int) $request->get('page', 1),
        'limit' => (int) $request->get('limit', 10)
    ];

    try {
        $model = new UserModel();
        $results = $model->search($filters);
        $response->sendJson(['data' => $results], 200);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Search failed'], 500);
        return false;
    }
}
```

### Pattern 4: File Operations

```php
public function downloadReport(Request $request, Response $response, array $middlewareData) : bool {
    
    try {
        $reportId = $request->paths(0);
        $filePath = $this->generateReport($reportId);
        
        $response->sendFile($filePath);
        return true;
    } catch (\Exception $e) {
        Logger::Log($e->getMessage(), Logger::ERROR);
        $response->sendJson(['error' => 'Download failed'], 500);
        return false;
    }
}
```

### Pattern 5: CLI with Progress Output

```php
public function processBulkData(Input $input, Output $output, array $middlewareData) : void {
    
    $batchSize = (int) $input->parsed('batch-size') ?? 100;
    $total = 1000;

    try {
        $output->stdout("Processing {$total} items...");
        
        for ($i = 0; $i < $total; $i += $batchSize) {
            $this->processBatch($i, $batchSize);
            $progress = min($i + $batchSize, $total);
            $output->stdout("Progress: {$progress}/{$total}");
        }

        $output->stdout("Processing completed successfully");
    } catch (\Exception $e) {
        $output->stderr("Processing failed: " . $e->getMessage());
    }
}
```

---

## Related Documentation

- [HTTP Workflow](./http-workflow.md) - Complete HTTP request lifecycle
- [CLI Workflow](./cli-workflow.md) - CLI command execution flow
- [Routing](./http-and-cli-routing.md) - How routes map to controllers
- [Middleware](./middleware.md) - Request processing before controllers
- [CRUD Development Tutorial](../tutorials/crud-development.md) - Step-by-step guide
