# Duktig PHP Microservice - Development documentation

## Tutorial - CRUD Development

This tutorial demonstrates how to build a complete RESTful API with CRUD (Create, Read, Update, Delete) operations in the Duktig PHP Microservice Framework.

> **Note**: This framework is designed primarily for JSON API development. All responses are in JSON format.

## Table of Contents

1. [Overview](#overview)
2. [What We'll Build](#what-well-build)
3. [Database Setup](#database-setup)
4. [Creating the Model](#creating-the-model)
5. [Creating the Controller](#creating-the-controller)
6. [Configuring Routes](#configuring-routes)
7. [Testing the API](#testing-the-api)
8. [Advanced Features](#advanced-features)

---

## Overview

In this tutorial, we'll build a complete **Tasks API** that demonstrates:
- **CREATE** - Add new tasks (POST)
- **READ** - Get all tasks and single task (GET)
- **UPDATE** - Update task details (PATCH)
- **DELETE** - Remove tasks (DELETE)
- Input validation
- Error handling
- JSON responses
- RESTful best practices

---

## What We'll Build

A **Tasks API** with the following endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/tasks` | Get all tasks (with pagination) |
| `GET` | `/api/tasks/{id}` | Get a single task by ID |
| `POST` | `/api/tasks` | Create a new task |
| `PATCH` | `/api/tasks/{id}` | Update a task (partial update) |
| `DELETE` | `/api/tasks/{id}` | Delete a task |

---

## Database Setup

### Step 1: Create the Database Table

#### For MySQL:

```sql
CREATE TABLE `tasks` (
  `task_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
  `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
  `due_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### For PostgreSQL:

```sql
CREATE TYPE task_status AS ENUM ('pending', 'in_progress', 'completed');
CREATE TYPE task_priority AS ENUM ('low', 'medium', 'high');

CREATE TABLE tasks (
  task_id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  status task_status DEFAULT 'pending',
  priority task_priority DEFAULT 'medium',
  due_date DATE DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_status ON tasks(status);
CREATE INDEX idx_due_date ON tasks(due_date);
```

### Step 2: Configure Database Connection

Add database configuration to `src/app/config/app.php`:

```php
'Databases' => [
    'Tasks_DB' => [
        'host'     => Env::get('DB_HOST', 'localhost'),
        'port'     => Env::get('DB_PORT', '3306'),
        'username' => Env::get('DB_USERNAME', 'root'),
        'password' => Env::get('DB_PASSWORD', ''),
        'database' => Env::get('DB_NAME', 'your_database'),
        'charset'  => 'utf8mb4'
    ]
]
```

---

## Creating the Model

### Step 1: Create the Model File

Create file: `src/app/Models/Tasks/Task.php`

```php
<?php
/**
 * Task Model
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Models\Tasks;

use Exception;
use System\Config;

/**
 * Class Task
 *
 * Model class for managing tasks in the database
 *
 * @package App\Models\Tasks
 */
class Task extends \Lib\Db\MySQLi {

    /**
     * Constructor
     */
    public function __construct() {
        $config = Config::get()['Databases']['Tasks_DB'];
        parent::__construct($config);
    }

    /**
     * Get all tasks with pagination
     *
     * @param int $offset Starting position
     * @param int $limit Number of records to fetch
     * @return array|bool
     * @throws Exception
     */
    public function fetchAll(int $offset = 0, int $limit = 50): array|bool {
        return $this->fetchAllAssoc(
            "SELECT task_id, title, description, status, priority, due_date,
                    created_at, updated_at
             FROM tasks
             ORDER BY created_at DESC
             LIMIT ?, ?",
            [$offset, $limit]
        );
    }

    /**
     * Get task by ID
     *
     * @param int $taskId
     * @return array|bool
     * @throws Exception
     */
    public function fetchById(int $taskId): array|bool {
        return $this->fetchAssoc(
            "SELECT task_id, title, description, status, priority, due_date,
                    created_at, updated_at
             FROM tasks
             WHERE task_id = ?",
            [$taskId]
        );
    }

    /**
     * Create a new task
     *
     * @param array $data Task data
     * @return int|bool Last insert ID or false on failure
     * @throws Exception
     */
    public function create(array $data): int|bool {
        return $this->insert('tasks', [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? 'pending',
            'priority'    => $data['priority'] ?? 'medium',
            'due_date'    => $data['due_date'] ?? null
        ]);
    }

    /**
     * Update task by ID
     *
     * @param int $taskId
     * @param array $data Fields to update
     * @return int Number of affected rows
     * @throws Exception
     */
    public function updateById(int $taskId, array $data): int {
        return $this->update('tasks', $data, ['task_id' => $taskId]);
    }

    /**
     * Delete task by ID
     *
     * @param int $taskId
     * @return int Number of affected rows
     * @throws Exception
     */
    public function deleteById(int $taskId): int {
        return $this->delete('tasks', ['task_id' => $taskId]);
    }

    /**
     * Check if task exists
     *
     * @param int $taskId
     * @return bool
     * @throws Exception
     */
    public function exists(int $taskId): bool {
        $result = $this->fetchAssoc(
            "SELECT COUNT(*) as count FROM tasks WHERE task_id = ?",
            [$taskId]
        );
        return $result && $result['count'] > 0;
    }

    /**
     * Get total tasks count
     *
     * @return int
     * @throws Exception
     */
    public function getTotalCount(): int {
        $result = $this->fetchAssoc("SELECT COUNT(*) as count FROM tasks");
        return $result ? (int)$result['count'] : 0;
    }
}
```

### For PostgreSQL:

If using PostgreSQL, extend `\Lib\Db\PostgreSQL` instead:

```php
class Task extends \Lib\Db\PostgreSQL {
    // ... same methods with PostgreSQL-specific syntax if needed
}
```

---

## Creating the Controller

### Step 1: Create the Controller File

Create file: `src/app/Controllers/Tasks/TasksController.php`

```php
<?php
/**
 * Tasks Controller
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Controllers\Tasks;

use Exception;
use Lib\Validator;
use System\HTTP\Request;
use System\HTTP\Response;
use App\Models\Tasks\Task as TaskModel;

/**
 * Class TasksController
 *
 * RESTful API controller for task management
 *
 * @package App\Controllers\Tasks
 */
class TasksController {

    /**
     * GET /api/tasks
     * Get all tasks with pagination
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws Exception
     */
    public function getAll(Request $request, Response $response, array $middlewareData): bool {

        // Get pagination parameters from query string
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 50);

        // Validate pagination parameters
        $validation = Validator::validateDataStructure(
            $request->get(),
            [
                'offset' => 'int_range:0',
                'limit' => 'int_range:1:100'
            ]
        );

        // Return validation errors
        if (!empty($validation)) {
            $response->sendJson($validation, 422);
            return false;
        }

        // Fetch tasks from database
        $taskModel = new TaskModel();
        $tasks = $taskModel->fetchAll($offset, $limit);
        $total = $taskModel->getTotalCount();

        // Send JSON response
        $response->sendJson([
            'status' => 'success',
            'data' => [
                'tasks' => $tasks ?: [],
                'total' => $total,
                'offset' => (int)$offset,
                'limit' => (int)$limit
            ]
        ], 200);

        return true;
    }

    /**
     * GET /api/tasks/{id}
     * Get a single task by ID
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws Exception
     */
    public function getById(Request $request, Response $response, array $middlewareData): bool {

        // Get task ID from URL path
        $taskId = $request->paths(2);

        // Validate task ID
        $validation = Validator::validateDataStructure(
            ['task_id' => $taskId],
            ['task_id' => 'id']
        );

        if (!empty($validation)) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid task ID'
            ], 400);
            return false;
        }

        // Fetch task from database
        $taskModel = new TaskModel();
        $task = $taskModel->fetchById($taskId);

        // Check if task exists
        if (!$task) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
            return false;
        }

        // Send JSON response
        $response->sendJson([
            'status' => 'success',
            'data' => $task
        ], 200);

        return true;
    }

    /**
     * POST /api/tasks
     * Create a new task
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws Exception
     */
    public function create(Request $request, Response $response, array $middlewareData): bool {

        // Validate request body
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'title' => 'string_length:1:255',
                'description' => 'string_length:0:5000:!required',
                'status' => 'one_of:pending:in_progress:completed:!required',
                'priority' => 'one_of:low:medium:high:!required',
                'due_date' => 'date:!required'
            ],
            [
                'general' => 'no_extra_values'
            ]
        );

        // Return validation errors
        if (!empty($validation)) {
            $response->sendJson([
                'status' => 'error',
                'errors' => $validation
            ], 422);
            return false;
        }

        // Create task in database
        $taskModel = new TaskModel();
        $taskId = $taskModel->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'pending'),
            'priority' => $request->input('priority', 'medium'),
            'due_date' => $request->input('due_date')
        ]);

        if (!$taskId) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Failed to create task'
            ], 500);
            return false;
        }

        // Fetch created task to return
        $task = $taskModel->fetchById($taskId);

        // Send JSON response
        $response->sendJson([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);

        return true;
    }

    /**
     * PATCH /api/tasks/{id}
     * Update a task (partial update)
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws Exception
     */
    public function update(Request $request, Response $response, array $middlewareData): bool {

        // Get task ID from URL path
        $taskId = $request->paths(2);

        // Validate task ID
        $validation = Validator::validateDataStructure(
            ['task_id' => $taskId],
            ['task_id' => 'id']
        );

        if (!empty($validation)) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid task ID'
            ], 400);
            return false;
        }

        // Check if task exists
        $taskModel = new TaskModel();
        if (!$taskModel->exists($taskId)) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
            return false;
        }

        // Validate request body (all fields optional for PATCH)
        $validation = Validator::validateJson(
            $request->rawInput(),
            [
                'title' => 'string_length:1:255:!required',
                'description' => 'string_length:0:5000:!required',
                'status' => 'one_of:pending:in_progress:completed:!required',
                'priority' => 'one_of:low:medium:high:!required',
                'due_date' => 'date:!required'
            ],
            [
                'general' => 'at_least_one_value|no_extra_values'
            ]
        );

        // Return validation errors
        if (!empty($validation)) {
            $response->sendJson([
                'status' => 'error',
                'errors' => $validation
            ], 422);
            return false;
        }

        // Build update data from request
        $allowedFields = ['title', 'description', 'status', 'priority', 'due_date'];
        $updateData = [];

        foreach ($request->input() as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updateData[$field] = $value;
            }
        }

        // Update task in database
        $taskModel->updateById($taskId, $updateData);

        // Fetch updated task
        $task = $taskModel->fetchById($taskId);

        // Send JSON response
        $response->sendJson([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'data' => $task
        ], 200);

        return true;
    }

    /**
     * DELETE /api/tasks/{id}
     * Delete a task
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     * @throws Exception
     */
    public function delete(Request $request, Response $response, array $middlewareData): bool {

        // Get task ID from URL path
        $taskId = $request->paths(2);

        // Validate task ID
        $validation = Validator::validateDataStructure(
            ['task_id' => $taskId],
            ['task_id' => 'id']
        );

        if (!empty($validation)) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid task ID'
            ], 400);
            return false;
        }

        // Check if task exists
        $taskModel = new TaskModel();
        if (!$taskModel->exists($taskId)) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
            return false;
        }

        // Delete task from database
        $taskModel->deleteById($taskId);

        // Send JSON response
        $response->sendJson([
            'status' => 'success',
            'message' => 'Task deleted successfully'
        ], 200);

        return true;
    }
}
```

---

## Configuring Routes

### Step 1: Add Routes to HTTP Configuration

Edit `src/app/config/http-routes.php`:

```php
return [

    // GET requests
    'GET' => [

        // Get all tasks
        '/api/tasks' => [
            'middleware' => [],
            'controller' => 'Tasks\TasksController->getAll'
        ],

        // Get single task by ID
        '/api/tasks/{id}' => [
            'middleware' => [],
            'controller' => 'Tasks\TasksController->getById'
        ],

    ],

    // POST requests
    'POST' => [

        // Create new task
        '/api/tasks' => [
            'middleware' => [],
            'controller' => 'Tasks\TasksController->create'
        ],

    ],

    // PATCH requests
    'PATCH' => [

        // Update task
        '/api/tasks/{id}' => [
            'middleware' => [],
            'controller' => 'Tasks\TasksController->update'
        ],

    ],

    // DELETE requests
    'DELETE' => [

        // Delete task
        '/api/tasks/{id}' => [
            'middleware' => [],
            'controller' => 'Tasks\TasksController->delete'
        ],

    ]

];
```

---

## Testing the API

### Using cURL

#### 1. Create a Task (POST)

```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete documentation",
    "description": "Write comprehensive CRUD tutorial",
    "status": "in_progress",
    "priority": "high",
    "due_date": "2024-12-31"
  }'
```

**Response (201 Created):**
```json
{
  "status": "success",
  "message": "Task created successfully",
  "data": {
    "task_id": "1",
    "title": "Complete documentation",
    "description": "Write comprehensive CRUD tutorial",
    "status": "in_progress",
    "priority": "high",
    "due_date": "2024-12-31",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

#### 2. Get All Tasks (GET)

```bash
curl -X GET "http://localhost/api/tasks?offset=0&limit=10"
```

**Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "tasks": [
      {
        "task_id": "1",
        "title": "Complete documentation",
        "description": "Write comprehensive CRUD tutorial",
        "status": "in_progress",
        "priority": "high",
        "due_date": "2024-12-31",
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
      }
    ],
    "total": 1,
    "offset": 0,
    "limit": 10
  }
}
```

#### 3. Get Single Task (GET)

```bash
curl -X GET http://localhost/api/tasks/1
```

**Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "task_id": "1",
    "title": "Complete documentation",
    "description": "Write comprehensive CRUD tutorial",
    "status": "in_progress",
    "priority": "high",
    "due_date": "2024-12-31",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

#### 4. Update Task (PATCH)

```bash
curl -X PATCH http://localhost/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed",
    "description": "Documentation is now complete!"
  }'
```

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Task updated successfully",
  "data": {
    "task_id": "1",
    "title": "Complete documentation",
    "description": "Documentation is now complete!",
    "status": "completed",
    "priority": "high",
    "due_date": "2024-12-31",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 11:45:00"
  }
}
```

#### 5. Delete Task (DELETE)

```bash
curl -X DELETE http://localhost/api/tasks/1
```

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Task deleted successfully"
}
```

### Error Response Examples

#### Validation Error (422 Unprocessable Entity)

```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "",
    "status": "invalid_status"
  }'
```

**Response:**
```json
{
  "status": "error",
  "errors": {
    "title": ["Title must be between 1 and 255 characters"],
    "status": ["Status must be one of: pending, in_progress, completed"]
  }
}
```

#### Not Found Error (404)

```bash
curl -X GET http://localhost/api/tasks/99999
```

**Response:**
```json
{
  "status": "error",
  "message": "Task not found"
}
```

#### Invalid ID Error (400)

```bash
curl -X GET http://localhost/api/tasks/abc
```

**Response:**
```json
{
  "status": "error",
  "message": "Invalid task ID"
}
```

---

## Advanced Features

### 1. Adding Middleware for Authentication

Create authentication middleware in `src/app/Middleware/Auth/JwtAuth.php`:

```php
<?php
namespace App\Middleware\Auth;

use System\HTTP\Request;
use System\HTTP\Response;
use Lib\Auth\Jwt;

class JwtAuth {

    public function verify(Request $request, Response $response, array $middlewareData): array|bool {

        $token = $request->header('Authorization');

        if (!$token) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Authorization token required'
            ], 401);
            return false;
        }

        try {
            $decoded = Jwt::decode($token, 'your-secret-key');
            $middlewareData['user'] = $decoded;
            return $middlewareData;
        } catch (\Exception $e) {
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 401);
            return false;
        }
    }
}
```

Add middleware to routes:

```php
'/api/tasks' => [
    'middleware' => [
        'Auth\JwtAuth->verify'
    ],
    'controller' => 'Tasks\TasksController->getAll'
],
```

### 2. Adding Filtering and Search

#### Update Controller Method

Extend the `getAll` method in the Controller:

```php
public function getAll(Request $request, Response $response, array $middlewareData): bool {

    // Build filter array with defaults
    $filters = [
        'offset' => $request->get('offset', 0),
        'limit' => $request->get('limit', 50),
        'status' => $request->get('status', ''),
        'search' => $request->get('search', '')
    ];

    // Validate filter parameters
    $validation = Validator::validateDataStructure(
        $filters,
        [
            'offset' => 'int_range:0',
            'limit' => 'int_range:1:100',
            'status' => 'one_of:pending:in_progress:completed:!required',
            'search' => 'string_length:0:255:!required'
        ]
    );

    if (!empty($validation)) {
        $response->sendJson($validation, 422);
        return false;
    }

    // Fetch tasks from model with filters
    $taskModel = new TaskModel();
    $tasks = $taskModel->fetchAllWithFilters($filters['offset'], $filters['limit'], $filters);
    $total = $taskModel->getTotalCount($filters);

    $response->sendJson([
        'status' => 'success',
        'data' => [
            'tasks' => $tasks ?: [],
            'total' => $total,
            'offset' => (int)$filters['offset'],
            'limit' => (int)$filters['limit']
        ]
    ], 200);

    return true;
}
```

#### Add Model Methods

Add filtering methods to the Model class:

```php
/**
 * Get all tasks with filters and pagination
 *
 * @param int $offset
 * @param int $limit
 * @param array $filters
 * @return array|bool
 * @throws Exception
 */
public function fetchAllWithFilters(int $offset = 0, int $limit = 50, array $filters = []): array|bool {

    $query = "SELECT task_id, title, description, status, priority, due_date,
                     created_at, updated_at
              FROM tasks
              WHERE 1=1";

    $params = [];

    // Add status filter
    if (!empty($filters['status'])) {
        $query .= " AND status = ?";
        $params[] = $filters['status'];
    }

    // Add search filter
    if (!empty($filters['search'])) {
        $query .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = "%{$filters['search']}%";
        $params[] = "%{$filters['search']}%";
    }

    $query .= " ORDER BY created_at DESC LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;

    return $this->fetchAllAssoc($query, $params);
}

/**
 * Get total count with filters
 *
 * @param array $filters
 * @return int
 * @throws Exception
 */
public function getTotalCount(array $filters = []): int {

    $query = "SELECT COUNT(*) as count FROM tasks WHERE 1=1";
    $params = [];

    // Add status filter
    if (!empty($filters['status'])) {
        $query .= " AND status = ?";
        $params[] = $filters['status'];
    }

    // Add search filter
    if (!empty($filters['search'])) {
        $query .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = "%{$filters['search']}%";
        $params[] = "%{$filters['search']}%";
    }

    $result = $this->fetchAssoc($query, $params);
    return $result ? (int)$result['count'] : 0;
}
```

### 3. Soft Deletes

Instead of hard deleting, add `deleted_at` column:

```sql
ALTER TABLE tasks ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
```

Update the delete method:

```php
public function delete(Request $request, Response $response, array $middlewareData): bool {

    $taskId = $request->paths(2);

    // ... validation ...

    // Soft delete
    $taskModel->updateById($taskId, [
        'deleted_at' => date('Y-m-d H:i:s')
    ]);

    $response->sendJson([
        'status' => 'success',
        'message' => 'Task deleted successfully'
    ], 200);

    return true;
}
```

### 4. Bulk Operations

Add bulk delete endpoint:

```php
public function bulkDelete(Request $request, Response $response, array $middlewareData): bool {

    $validation = Validator::validateJson(
        $request->rawInput(),
        [
            'task_ids' => 'array',
            'task_ids.*' => 'id'
        ]
    );

    if (!empty($validation)) {
        $response->sendJson($validation, 422);
        return false;
    }

    $taskModel = new TaskModel();
    $taskIds = $request->input('task_ids');

    foreach ($taskIds as $taskId) {
        $taskModel->deleteById($taskId);
    }

    $response->sendJson([
        'status' => 'success',
        'message' => count($taskIds) . ' tasks deleted successfully'
    ], 200);

    return true;
}
```

---

## Best Practices

### 1. **Always Validate Input**
Never trust user input. Use `Lib\Validator` for all incoming data.

### 2. **Use Proper HTTP Status Codes**
- `200` - OK (successful GET, PATCH, DELETE)
- `201` - Created (successful POST)
- `400` - Bad Request (invalid input format)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation errors)
- `500` - Internal Server Error (unexpected errors)

### 3. **Return Consistent JSON Structure**
```json
{
  "status": "success|error",
  "message": "Human readable message",
  "data": {},
  "errors": {}
}
```

### 4. **Use Transactions for Complex Operations**
```php
$taskModel->beginTrans();
try {
    $taskModel->create($data1);
    $taskModel->update($data2);
    $taskModel->commitTrans();
} catch (\Throwable $e) {
    $taskModel->rollbackTrans();
    throw $e;
}
```

### 5. **Implement Pagination**
Always limit query results and provide pagination for list endpoints.

### 6. **Add Rate Limiting**
Protect your API from abuse using middleware.

### 7. **Log Important Actions**
Use the framework's logging system for debugging and audit trails.

---

## Summary

You've now learned how to:

- Create database tables with proper indexes
- Build Model classes extending framework DB classes
- Create RESTful Controllers with all CRUD operations
- Configure HTTP routes
- Validate input data
- Handle errors gracefully
- Return proper JSON responses
- Test API endpoints with cURL
- Implement advanced features (auth, filtering, soft deletes)

---

## Next Steps

- **Add Authentication**: Implement JWT-based authentication
- **Add Permissions**: Role-based access control
- **Add Caching**: Cache frequently accessed data using Redis
- **Add Tests**: Write unit tests for your API
- **Add Documentation**: Generate OpenAPI/Swagger documentation
- **Add Logging**: Track all API requests and errors

## Related Documentation

- [Data Validator](../kernel/libraries/valid.md)
- [Data Structures Validator](../kernel/libraries/validator.md)
- [MySQL Library](../kernel/libraries/db/mysqli.md)
- [PostgreSQL Library](../kernel/libraries/db/postgresql.md)
- [HTTP Workflow](../development/http-workflow.md)
- [HTTP and CLI Routing](../development/http-and-cli-routing.md)
