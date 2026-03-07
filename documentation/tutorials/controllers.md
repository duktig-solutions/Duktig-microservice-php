# Duktig PHP Microservice - Development Documentation

## Tutorial - Controllers Development

This tutorial demonstrates how to create, configure, and use controllers in the Duktig PHP Microservice Framework. We'll build a complete Blog API with controllers handling different operations, from simple retrieval to complex business logic.

> **Prerequisites**: Familiarity with the Duktig microservice structure and how routing works. See [Controllers Documentation](../development/controllers.md) for conceptual overview.

## Table of Contents

1. [Overview](#overview)
2. [Understanding Controllers Basics](#understanding-controllers-basics)
3. [Project Setup](#project-setup)
4. [Creating Your First HTTP Controller](#creating-your-first-http-controller)
5. [Working with Request Data](#working-with-request-data)
6. [Response Handling](#response-handling)
7. [Error Handling & Validation](#error-handling--validation)
8. [Building CRUD Controllers](#building-crud-controllers)
9. [Working with Middleware Data](#working-with-middleware-data)
10. [Creating CLI Controllers](#creating-cli-controllers)
11. [Testing Controllers](#testing-controllers)
12. [Best Practices](#best-practices)

---

## Overview

In this tutorial, we'll create a complete **Blog API** that demonstrates:

- **HTTP Controller creation** - Build RESTful endpoints
- **Request handling** - Access GET, POST, JSON data, headers
- **Response formatting** - Send JSON responses with appropriate status codes
- **Input validation** - Validate user data before processing
- **Error handling** - Handle errors gracefully with proper status codes
- **CRUD operations** - Create, read, update, delete blog posts
- **Middleware integration** - Receive and use data from middleware
- **CLI controllers** - Build background job handlers
- **Testing** - Test your controllers with real examples

### What You'll Learn

- How to structure HTTP controllers for REST APIs
- How to access request data (GET, POST, JSON, headers, paths)
- How to validate input before processing
- How to return proper HTTP responses
- How to handle errors and edge cases
- How to work with middleware-provided data
- How to build CLI controllers for background jobs
- How to debug and test controllers

---

## Understanding Controllers Basics

### HTTP Controller Execution Flow

```
1. Client sends HTTP Request (GET, POST, PUT, PATCH, DELETE)
   ↓
2. Request is routed to matching endpoint
   ↓
3. Middleware chain executes (in order)
   ├─ Each middleware can validate, authenticate, or inject data
   └─ Returns modified $middlewareData
   ↓
4. Controller method executes
   ├─ Receives: Request, Response, $middlewareData
   ├─ Processes business logic
   └─ Returns response to client
   ↓
5. Response sent to client
```

### Controller Method Signature

Every HTTP controller method must follow this signature:

```php
public function methodName(
    Request $request,          // Access to incoming request data
    Response $response,        // For sending responses
    array $middlewareData      // Data from middleware chain
) : bool                       // Return true/false to indicate success
```

### Request Object - Available Methods

The `Request` object provides access to all incoming HTTP data:

```php
// HTTP Method and URI
$method = $request->method();           // "GET", "POST", etc.
$uri = $request->uri();                 // Full URL: http://localhost/api/posts

// URL Path segments
$paths = $request->paths();             // Array of path segments
$postId = $request->paths(0);           // First segment (if exists)

// Query string parameters (?key=value)
$allGet = $request->get();              // All query parameters
$page = $request->get('page');          // Specific parameter
$limit = $request->get('limit', 10);    // With default value

// POST/JSON request body
$allInput = $request->input();          // Parsed POST or JSON data
$title = $request->input('title');      // Specific field
$content = $request->input('content', '');  // With default

// Raw request body (for JSON)
$rawJson = $request->rawInput();        // Unparsed body string

// Headers
$allHeaders = $request->headers();      // All headers
$token = $request->headers('Authorization');  // Specific header
$token = $request->headers('Authorization', 'none');  // With default

// Server variables
$userAgent = $request->server('HTTP_USER_AGENT');
$host = $request->server('HTTP_HOST');

// POST data (form-encoded)
$allPost = $request->post();            // All POST data
$username = $request->post('username');  // Specific POST field
```

### Response Object - Available Methods

The `Response` object is used to send responses back to the client:

```php
// Send JSON response
$response->sendJson(
    ['status' => 'success', 'data' => $data],
    200  // HTTP status code
);

// Send file for download
$response->sendFile('/path/to/file.pdf');

// Set HTTP status code
$response->status(201);  // Created

// Set custom header
$response->header('X-Custom-Header', 'value');

// Write raw content
$response->write('Some content');

// Send response immediately and exit
$response->sendFinal();  // Must call after setting everything
```

---

## Project Setup

### Step 1: Create Directory Structure

For this tutorial, we'll create controllers in:

```
src/app/Controllers/
  └── Tutorial/
      ├── Blog/
      │   ├── Posts.php
      │   ├── Comments.php
      │   └── Categories.php
      └── Admin/
          └── Dashboard.php
```

Create the directories:

```bash
mkdir -p src/app/Controllers/Tutorial/Blog
mkdir -p src/app/Controllers/Tutorial/Admin
```

### Step 2: Create Database Table (Reference)

For this tutorial, we'll assume a `posts` table:

```sql
CREATE TABLE posts (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    author_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_status (status),
    KEY idx_author (author_id)
);
```

### Step 3: Create a Simple Post Model (Reference)

Create `src/app/Models/Blog/Post.php`:

```php
<?php
namespace App\Models\Blog;

use Lib\Db\MySQLi;
use System\Config;

class Post extends MySQLi {
    
    public function __construct() {
        $config = Config::get()['Databases']['DefaultDB'];
        parent::__construct($config);
    }

    public function getAll($limit = 10, $offset = 0) {
        return $this->query(
            "SELECT * FROM posts WHERE status = 'published' LIMIT ? OFFSET ?",
            [$limit, $offset],
            ['i', 'i']
        );
    }

    public function getById($id) {
        return $this->queryFirst(
            "SELECT * FROM posts WHERE id = ? AND status = 'published'",
            [$id],
            ['i']
        );
    }

    public function create($data) {
        $this->insert('posts', [
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => 'draft',
            'author_id' => $data['author_id']
        ]);
        return $this->insertId();
    }

    public function update($id, $data) {
        $this->update('posts', $data, ['id' => $id]);
        return $this->affectedRows();
    }

    public function delete($id) {
        $this->delete('posts', ['id' => $id]);
        return $this->affectedRows();
    }
}
```

---

## Creating Your First HTTP Controller

### Step 1: Create a Simple GET Controller

This is the simplest controller - it retrieves data and returns it.

**File**: `src/app/Controllers/Tutorial/Blog/Posts.php`

```php
<?php
/**
 * Blog Posts Controller
 *
 * Handles blog post operations (GET, POST, PATCH, DELETE)
 *
 * @author Your Name <your@email.com>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Tutorial\Blog;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Logger;
use App\Models\Blog\Post as PostModel;

/**
 * Class Posts
 *
 * @package App\Controllers\Tutorial\Blog
 */
class Posts {
    
    /**
     * Get all published posts with pagination
     *
     * GET /api/posts?page=1&limit=10
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function getAll(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            // Get pagination parameters from query string
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 10);
            
            // Validate pagination
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 100) $limit = 10;
            
            // Calculate offset
            $offset = ($page - 1) * $limit;
            
            // Fetch posts from database
            $postModel = new PostModel();
            $posts = $postModel->getAll($limit, $offset);
            
            // Send successful response
            $response->sendJson([
                'status' => 'success',
                'data' => $posts,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => count($posts)
                ]
            ], 200);
            
            return true;
            
        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Failed to retrieve posts'
            ], 500);
            
            return false;
        }
    }

    /**
     * Get a single post by ID
     *
     * GET /api/posts/{id}
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function getById(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            // Get post ID from URL path
            $postId = $request->paths(0);
            
            if (!$postId || !is_numeric($postId)) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'Invalid post ID'
                ], 400);
                
                return false;
            }
            
            // Fetch post from database
            $postModel = new PostModel();
            $post = $postModel->getById((int) $postId);
            
            if (!$post) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'Post not found'
                ], 404);
                
                return false;
            }
            
            // Send post data
            $response->sendJson([
                'status' => 'success',
                'data' => $post
            ], 200);
            
            return true;
            
        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Server error'
            ], 500);
            
            return false;
        }
    }
}
```

### Step 2: Configure Routes

Edit `src/app/config/http-routes.php` and add:

```php
'GET' => [
    '/api/posts' => [
        'middleware' => [],
        'controller' => 'Tutorial\Blog\Posts->getAll'
    ],
    
    '/api/posts/{id}' => [
        'middleware' => [],
        'controller' => 'Tutorial\Blog\Posts->getById'
    ]
]
```

### Step 3: Test the Controller

Test with cURL:

```bash
# Get all posts
curl http://localhost/api/posts

# Get all posts with pagination
curl http://localhost/api/posts?page=1&limit=5

# Get a single post
curl http://localhost/api/posts/123
```

---

## Working with Request Data

### Accessing Different Request Data Types

**File**: `src/app/Controllers/Tutorial/Blog/RequestDemo.php`

```php
<?php
namespace App\Controllers\Tutorial\Blog;

use System\HTTP\Request;
use System\HTTP\Response;

class RequestDemo {
    
    /**
     * Demonstrate accessing different request data
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function demo(Request $request, Response $response, array $middlewareData) : bool {
        
        // 1. HTTP Method
        $method = $request->method();  // "GET", "POST", "PUT", etc.
        
        // 2. URL Path segments
        $allPaths = $request->paths();           // Array: [0, 1, 2, ...]
        $firstSegment = $request->paths(0);      // First segment
        $secondSegment = $request->paths(1);     // Second segment
        
        // 3. Query string parameters (GET requests or ?param=value)
        $allParams = $request->get();            // All query parameters
        $search = $request->get('search');       // Specific parameter
        $sort = $request->get('sort', 'asc');    // With default value
        
        // 4. Request body data (POST/PUT/PATCH with form data or JSON)
        $allData = $request->input();            // All request body data
        $title = $request->input('title');       // Specific field
        $desc = $request->input('description', '');  // With default
        
        // 5. Raw request body (for manual parsing)
        $rawBody = $request->rawInput();         // Raw string from php://input
        
        // 6. Headers
        $allHeaders = $request->headers();       // All headers
        $token = $request->headers('Authorization');      // Specific header
        $token = $request->headers('Authorization', 'none');  // With default
        $contentType = $request->headers('Content-Type');
        
        // 7. Server variables
        $host = $request->server('HTTP_HOST');
        $userAgent = $request->server('HTTP_USER_AGENT');
        
        $response->sendJson([
            'method' => $method,
            'paths' => $allPaths,
            'params' => $allParams,
            'headers' => $allHeaders,
            'title' => $title,
            'authorization' => $token
        ], 200);
        
        return true;
    }
}
```

---

## Response Handling

### Different Response Scenarios

**File**: `src/app/Controllers/Tutorial/Blog/ResponseDemo.php`

```php
<?php
namespace App\Controllers\Tutorial\Blog;

use System\HTTP\Request;
use System\HTTP\Response;

class ResponseDemo {
    
    /**
     * Send JSON response with success data
     */
    public function successResponse(Request $request, Response $response, array $middlewareData) : bool {
        
        $response->sendJson([
            'status' => 'success',
            'message' => 'Operation completed',
            'data' => [
                'id' => 123,
                'title' => 'Blog Post',
                'content' => 'Post content here'
            ]
        ], 200);  // HTTP 200 OK
        
        return true;
    }

    /**
     * Send JSON response with created resource (201)
     */
    public function createdResponse(Request $request, Response $response, array $middlewareData) : bool {
        
        $newPostId = 456;
        
        $response->sendJson([
            'status' => 'success',
            'message' => 'Post created',
            'data' => [
                'id' => $newPostId,
                'title' => 'New Post'
            ]
        ], 201);  // HTTP 201 Created
        
        return true;
    }

    /**
     * Send error response with 400 Bad Request
     */
    public function badRequestResponse(Request $request, Response $response, array $middlewareData) : bool {
        
        $response->sendJson([
            'status' => 'error',
            'message' => 'Invalid request data',
            'errors' => [
                'title' => 'Title is required',
                'content' => 'Content must be at least 10 characters'
            ]
        ], 400);  // HTTP 400 Bad Request
        
        return false;
    }

    /**
     * Send error response with 404 Not Found
     */
    public function notFoundResponse(Request $request, Response $response, array $middlewareData) : bool {
        
        $response->sendJson([
            'status' => 'error',
            'message' => 'Post not found'
        ], 404);  // HTTP 404 Not Found
        
        return false;
    }

    /**
     * Send file for download
     */
    public function downloadFile(Request $request, Response $response, array $middlewareData) : void {
        
        $filePath = '/path/to/export.pdf';
        
        // This will download the file and exit
        $response->sendFile($filePath);
    }

    /**
     * Send response with custom headers
     */
    public function customHeaderResponse(Request $request, Response $response, array $middlewareData) : bool {
        
        // Set custom headers BEFORE sending JSON
        $response->header('X-API-Version', '1.0');
        $response->header('X-Request-ID', 'req-12345');
        
        // Set status code
        $response->status(200);
        
        // Send JSON response
        $response->sendJson([
            'status' => 'success',
            'message' => 'Response with custom headers'
        ]);
        
        return true;
    }
}
```

---

## Error Handling & Validation

### Validating Request Data

**File**: `src/app/Controllers/Tutorial/Blog/PostsWithValidation.php`

```php
<?php
namespace App\Controllers\Tutorial\Blog;

use System\HTTP\Request;
use System\HTTP\Response;
use Lib\Validator;
use System\Logger;
use App\Models\Blog\Post as PostModel;

class PostsWithValidation {
    
    /**
     * Create a new post with validation
     *
     * POST /api/posts
     * Body: { "title": "...", "content": "...", "author_id": 1 }
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function create(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            // Step 1: Validate input using Validator library
            $validation = Validator::validateJson(
                $request->rawInput(),
                [
                    'title' => 'required|string_length:5:255',
                    'content' => 'required|string_length:20:10000',
                    'author_id' => 'required|id'
                ]
            );

            // Step 2: If validation fails, send error response
            if (!empty($validation)) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation
                ], 422);  // HTTP 422 Unprocessable Entity
                
                return false;
            }

            // Step 3: Get validated data
            $data = [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'author_id' => (int) $request->input('author_id')
            ];

            // Step 4: Create post in database
            $postModel = new PostModel();
            $postId = $postModel->create($data);

            // Step 5: Log successful creation
            Logger::Log("Post created: ID=$postId by Author={$data['author_id']}", Logger::INFO);

            // Step 6: Send success response
            $response->sendJson([
                'status' => 'success',
                'message' => 'Post created successfully',
                'data' => [
                    'id' => $postId,
                    'title' => $data['title']
                ]
            ], 201);  // HTTP 201 Created

            return true;

        } catch (\Exception $e) {
            Logger::Log("Error creating post: " . $e->getMessage(), Logger::ERROR);
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Failed to create post'
            ], 500);
            
            return false;
        }
    }

    /**
     * Update a post with validation
     *
     * PATCH /api/posts/{id}
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @return bool
     */
    public function update(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            // Get post ID from URL
            $postId = $request->paths(0);
            
            if (!$postId || !is_numeric($postId)) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'Invalid post ID'
                ], 400);
                
                return false;
            }

            // Validate update fields (all optional, but at least one required)
            $validation = Validator::validateJson(
                $request->rawInput(),
                [
                    'title' => 'string_length:5:255:!required',
                    'content' => 'string_length:20:10000:!required',
                    'status' => 'in:draft,published,archived:!required'
                ]
            );

            if (!empty($validation)) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation
                ], 422);
                
                return false;
            }

            // Check post exists
            $postModel = new PostModel();
            $post = $postModel->getById((int) $postId);
            
            if (!$post) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'Post not found'
                ], 404);
                
                return false;
            }

            // Build update data (only provided fields)
            $updateData = [];
            if ($request->input('title')) {
                $updateData['title'] = $request->input('title');
            }
            if ($request->input('content')) {
                $updateData['content'] = $request->input('content');
            }
            if ($request->input('status')) {
                $updateData['status'] = $request->input('status');
            }

            if (empty($updateData)) {
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'No fields to update'
                ], 422);
                
                return false;
            }

            // Update post
            $postModel->update((int) $postId, $updateData);

            Logger::Log("Post updated: ID=$postId", Logger::INFO);

            $response->sendJson([
                'status' => 'success',
                'message' => 'Post updated successfully'
            ], 200);

            return true;

        } catch (\Exception $e) {
            Logger::Log("Error updating post: " . $e->getMessage(), Logger::ERROR);
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Failed to update post'
            ], 500);
            
            return false;
        }
    }
}
```

---

## Building CRUD Controllers

### Complete CRUD Controller Example

**File**: `src/app/Controllers/Tutorial/Blog/PostsCRUD.php`

```php
<?php
/**
 * Posts CRUD Controller
 *
 * Handles all CRUD operations for blog posts
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Controllers\Tutorial\Blog;

use System\HTTP\Request;
use System\HTTP\Response;
use Lib\Validator;
use System\Logger;
use App\Models\Blog\Post as PostModel;

class PostsCRUD {

    /**
     * CREATE - Add new post
     * POST /api/posts
     */
    public function create(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            $validation = Validator::validateJson(
                $request->rawInput(),
                [
                    'title' => 'required|string_length:5:255',
                    'content' => 'required|string_length:20:10000'
                ]
            );

            if (!empty($validation)) {
                $response->sendJson(['status' => 'error', 'errors' => $validation], 422);
                return false;
            }

            $postModel = new PostModel();
            $postId = $postModel->create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'author_id' => $middlewareData['user']['id'] ?? 1
            ]);

            Logger::Log("Post created: $postId", Logger::INFO);

            $response->sendJson([
                'status' => 'success',
                'message' => 'Post created',
                'data' => ['id' => $postId]
            ], 201);

            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Creation failed'], 500);
            return false;
        }
    }

    /**
     * READ - Get all posts
     * GET /api/posts?page=1&limit=10
     */
    public function readAll(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 10);
            
            $postModel = new PostModel();
            $posts = $postModel->getAll($limit, ($page - 1) * $limit);

            $response->sendJson([
                'status' => 'success',
                'data' => $posts,
                'page' => $page,
                'limit' => $limit
            ], 200);

            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Read failed'], 500);
            return false;
        }
    }

    /**
     * READ - Get single post
     * GET /api/posts/{id}
     */
    public function readOne(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            $postId = $request->paths(0);
            
            if (!$postId || !is_numeric($postId)) {
                $response->sendJson(['status' => 'error', 'message' => 'Invalid ID'], 400);
                return false;
            }

            $postModel = new PostModel();
            $post = $postModel->getById((int) $postId);

            if (!$post) {
                $response->sendJson(['status' => 'error', 'message' => 'Not found'], 404);
                return false;
            }

            $response->sendJson(['status' => 'success', 'data' => $post], 200);
            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Read failed'], 500);
            return false;
        }
    }

    /**
     * UPDATE - Update existing post
     * PATCH /api/posts/{id}
     */
    public function update(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            $postId = $request->paths(0);
            
            if (!$postId || !is_numeric($postId)) {
                $response->sendJson(['status' => 'error', 'message' => 'Invalid ID'], 400);
                return false;
            }

            $validation = Validator::validateJson(
                $request->rawInput(),
                [
                    'title' => 'string_length:5:255:!required',
                    'content' => 'string_length:20:10000:!required'
                ]
            );

            if (!empty($validation)) {
                $response->sendJson(['status' => 'error', 'errors' => $validation], 422);
                return false;
            }

            $postModel = new PostModel();
            $post = $postModel->getById((int) $postId);

            if (!$post) {
                $response->sendJson(['status' => 'error', 'message' => 'Not found'], 404);
                return false;
            }

            $updateData = [];
            if ($request->input('title')) {
                $updateData['title'] = $request->input('title');
            }
            if ($request->input('content')) {
                $updateData['content'] = $request->input('content');
            }

            if (empty($updateData)) {
                $response->sendJson(['status' => 'error', 'message' => 'No data to update'], 422);
                return false;
            }

            $postModel->update((int) $postId, $updateData);
            Logger::Log("Post updated: $postId", Logger::INFO);

            $response->sendJson(['status' => 'success', 'message' => 'Updated'], 200);
            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Update failed'], 500);
            return false;
        }
    }

    /**
     * DELETE - Remove post
     * DELETE /api/posts/{id}
     */
    public function delete(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            $postId = $request->paths(0);
            
            if (!$postId || !is_numeric($postId)) {
                $response->sendJson(['status' => 'error', 'message' => 'Invalid ID'], 400);
                return false;
            }

            $postModel = new PostModel();
            $post = $postModel->getById((int) $postId);

            if (!$post) {
                $response->sendJson(['status' => 'error', 'message' => 'Not found'], 404);
                return false;
            }

            $postModel->delete((int) $postId);
            Logger::Log("Post deleted: $postId", Logger::INFO);

            $response->sendJson(['status' => 'success', 'message' => 'Deleted'], 200);
            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Delete failed'], 500);
            return false;
        }
    }
}
```

### Configure CRUD Routes

Add to `src/app/config/http-routes.php`:

```php
'POST' => [
    '/api/posts' => [
        'middleware' => ['Auth\Authenticate->verify'],
        'controller' => 'Tutorial\Blog\PostsCRUD->create'
    ]
],
'GET' => [
    '/api/posts' => [
        'middleware' => [],
        'controller' => 'Tutorial\Blog\PostsCRUD->readAll'
    ],
    '/api/posts/{id}' => [
        'middleware' => [],
        'controller' => 'Tutorial\Blog\PostsCRUD->readOne'
    ]
],
'PATCH' => [
    '/api/posts/{id}' => [
        'middleware' => ['Auth\Authenticate->verify'],
        'controller' => 'Tutorial\Blog\PostsCRUD->update'
    ]
],
'DELETE' => [
    '/api/posts/{id}' => [
        'middleware' => ['Auth\Authenticate->verify'],
        'controller' => 'Tutorial\Blog\PostsCRUD->delete'
    ]
]
```

---

## Working with Middleware Data

### Accessing and Using Middleware Data

**File**: `src/app/Controllers/Tutorial/Blog/PostsWithAuth.php`

```php
<?php
namespace App\Controllers\Tutorial\Blog;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Logger;
use App\Models\Blog\Post as PostModel;

class PostsWithAuth {

    /**
     * Create post - Using user from middleware
     *
     * Middleware has already:
     * - Authenticated the user
     * - Injected user data into $middlewareData
     *
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData Contains: ['user' => [...]]
     * @return bool
     */
    public function create(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            // Get user from middleware data
            // (injected by Authentication middleware)
            if (empty($middlewareData['user'])) {
                $response->sendJson(['status' => 'error', 'message' => 'User not authenticated'], 401);
                return false;
            }

            $user = $middlewareData['user'];
            $userId = $user['id'];
            $userName = $user['name'];

            // Create post with current user as author
            $postModel = new PostModel();
            $postId = $postModel->create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'author_id' => $userId
            ]);

            // Log with user information
            Logger::Log(
                "Post created by $userName (ID=$userId): Post ID=$postId",
                Logger::INFO
            );

            $response->sendJson([
                'status' => 'success',
                'message' => 'Post created by ' . $userName,
                'data' => ['id' => $postId]
            ], 201);

            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Failed'], 500);
            return false;
        }
    }

    /**
     * Get user's own posts
     *
     * Uses user ID from middleware to filter posts
     */
    public function getMyPosts(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            if (empty($middlewareData['user'])) {
                $response->sendJson(['status' => 'error', 'message' => 'Unauthorized'], 401);
                return false;
            }

            $userId = $middlewareData['user']['id'];

            // Fetch posts for this user
            $postModel = new PostModel();
            $posts = $postModel->getByAuthor($userId);

            $response->sendJson([
                'status' => 'success',
                'message' => 'Your posts',
                'data' => $posts
            ], 200);

            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Failed'], 500);
            return false;
        }
    }

    /**
     * Example: Using multiple middleware data
     *
     * $middlewareData might contain:
     * - ['user'] from Auth middleware
     * - ['permissions'] from Permission middleware
     * - ['requestTime'] from logging middleware
     */
    public function publishPost(Request $request, Response $response, array $middlewareData) : bool {
        
        try {
            $user = $middlewareData['user'] ?? null;
            $permissions = $middlewareData['permissions'] ?? [];
            $requestTime = $middlewareData['requestTime'] ?? date('Y-m-d H:i:s');

            // Check if user has publish permission
            if (!in_array('publish_posts', $permissions)) {
                Logger::Log("Unauthorized publish attempt by {$user['id']}", Logger::WARNING);
                
                $response->sendJson([
                    'status' => 'error',
                    'message' => 'You do not have permission to publish posts'
                ], 403);
                
                return false;
            }

            $postId = $request->paths(0);
            $postModel = new PostModel();
            $postModel->update((int) $postId, ['status' => 'published']);

            Logger::Log(
                "Post $postId published by {$user['name']} at $requestTime",
                Logger::INFO
            );

            $response->sendJson(['status' => 'success', 'message' => 'Published'], 200);
            return true;

        } catch (\Exception $e) {
            Logger::Log($e->getMessage(), Logger::ERROR);
            $response->sendJson(['status' => 'error', 'message' => 'Failed'], 500);
            return false;
        }
    }
}
```

---

## Creating CLI Controllers

### Simple CLI Controller

**File**: `src/app/Controllers/Tutorial/Blog/PostsBackup.php`

```php
<?php
/**
 * Posts Backup Controller
 *
 * CLI controller for backing up blog posts
 *
 * @author Your Name <your@email.com>
 * @version 1.0.0
 */
namespace App\Controllers\Tutorial\Blog;

use System\CLI\Input;
use System\CLI\Output;
use System\Logger;
use App\Models\Blog\Post as PostModel;

class PostsBackup {

    /**
     * Backup posts to file
     *
     * Usage: php cli/exec.php backup-posts --format json
     *
     * @param Input $input      CLI arguments (--format json)
     * @param Output $output    Console output
     * @param array $middlewareData  Data from middleware
     * @return void
     */
    public function backup(Input $input, Output $output, array $middlewareData) : void {
        
        try {
            // Get format from arguments
            $format = $input->parsed('format') ?? 'json';
            
            $output->stdout("Starting backup of posts...");
            $output->stdout("Format: $format");

            // Fetch all posts
            $postModel = new PostModel();
            $posts = $postModel->getAll(1000, 0);  // Get all posts

            // Generate filename
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "/backups/posts_backup_{$timestamp}.$format";

            // Save based on format
            if ($format === 'json') {
                file_put_contents($filename, json_encode($posts, JSON_PRETTY_PRINT));
            } else {
                $output->stderr("Unsupported format: $format");
                return;
            }

            $count = count($posts);
            $output->stdout("✓ Backup completed successfully");
            $output->stdout("Backed up $count posts to: $filename");

            Logger::Log("Posts backup completed: $count posts to $filename", Logger::INFO);

        } catch (\Exception $e) {
            $output->stderr("Backup failed: " . $e->getMessage());
            Logger::Log("Backup failed: " . $e->getMessage(), Logger::ERROR);
        }
    }

    /**
     * Process posts with batch parameters
     *
     * Usage: php cli/exec.php process-posts --batch-size 50 --action publish
     */
    public function processPosts(Input $input, Output $output, array $middlewareData) : void {
        
        try {
            $batchSize = (int) $input->parsed('batch-size') ?? 50;
            $action = $input->parsed('action') ?? 'count';

            $output->stdout("Processing posts...");
            $output->stdout("Batch size: $batchSize");
            $output->stdout("Action: $action");

            $postModel = new PostModel();
            $total = $postModel->count();

            $output->stdout("Total posts: $total");

            $processed = 0;
            for ($offset = 0; $offset < $total; $offset += $batchSize) {
                $batch = $postModel->getAll($batchSize, $offset);

                if ($action === 'publish') {
                    foreach ($batch as $post) {
                        $postModel->update($post['id'], ['status' => 'published']);
                        $processed++;
                    }
                }

                $progress = min($offset + $batchSize, $total);
                $output->stdout("Progress: $progress / $total");
            }

            $output->stdout("✓ Processing completed");
            $output->stdout("Total processed: $processed posts");

            Logger::Log("Posts processed: $processed with action=$action", Logger::INFO);

        } catch (\Exception $e) {
            $output->stderr("Processing failed: " . $e->getMessage());
        }
    }
}
```

### Configure CLI Routes

Add to `src/app/config/cli-routes.php`:

```php
'backup-posts' => [
    'controller' => 'Tutorial\Blog\PostsBackup->backup',
    'middleware' => [],
    'executeUniqueProcessLifeTime' => 30
],

'process-posts' => [
    'controller' => 'Tutorial\Blog\PostsBackup->processPosts',
    'middleware' => [],
    'executeUniqueProcessLifeTime' => 60
]
```

### Test CLI Controller

```bash
# Backup posts
php cli/exec.php backup-posts --format json

# Process posts
php cli/exec.php process-posts --batch-size 100 --action publish
```

---

## Testing Controllers

### Manual Testing with cURL

```bash
# CREATE - Add new post
curl -X POST http://localhost/api/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-token" \
  -d '{
    "title": "My New Post",
    "content": "This is the content of my first blog post..."
  }'

# READ ALL - Get posts with pagination
curl http://localhost/api/posts?page=1&limit=10

# READ ONE - Get single post
curl http://localhost/api/posts/123

# UPDATE - Update post
curl -X PATCH http://localhost/api/posts/123 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-token" \
  -d '{
    "title": "Updated Title",
    "status": "published"
  }'

# DELETE - Remove post
curl -X DELETE http://localhost/api/posts/123 \
  -H "Authorization: Bearer your-token"
```

### Testing Error Scenarios

```bash
# Test with invalid data (validation error)
curl -X POST http://localhost/api/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "A"
  }' 
# Expected: 422 Unprocessable Entity

# Test with missing post (not found)
curl http://localhost/api/posts/99999
# Expected: 404 Not Found

# Test without authentication
curl -X POST http://localhost/api/posts \
  -H "Content-Type: application/json" \
  -d '{"title": "Test", "content": "Test content"}'
# Expected: 401 Unauthorized
```

---

## Best Practices

### 1. Always Validate Input

```php
// GOOD - Validate before using
$validation = Validator::validateJson($request->rawInput(), [...]);
if (!empty($validation)) {
    $response->sendJson(['errors' => $validation], 422);
    return false;
}

// BAD - Use input without validation
$title = $request->input('title');
$postModel->create(['title' => $title]);
```

### 2. Return Appropriate HTTP Status Codes

```php
// GOOD - Use correct status codes
$response->sendJson($data, 200);        // Success
$response->sendJson($data, 201);        // Created
$response->sendJson($error, 400);       // Bad Request
$response->sendJson($error, 401);       // Unauthorized
$response->sendJson($error, 403);       // Forbidden
$response->sendJson($error, 404);       // Not Found
$response->sendJson($error, 422);       // Validation Error
$response->sendJson($error, 500);       // Server Error

// BAD - Always returning 200
$response->sendJson($data, 200);
$response->sendJson($error, 200);       // Wrong!
```

### 3. Use Try-Catch for Error Handling

```php
// GOOD - Catch exceptions
try {
    $result = $postModel->create($data);
    $response->sendJson(['data' => $result], 201);
} catch (\Exception $e) {
    Logger::Log($e->getMessage(), Logger::ERROR);
    $response->sendJson(['error' => 'Failed'], 500);
}

// BAD - No error handling
$result = $postModel->create($data);
$response->sendJson(['data' => $result], 200);
```

### 4. Log Important Operations

```php
// GOOD - Log key operations
Logger::Log("Post created: ID=$postId by User=$userId", Logger::INFO);
Logger::Log("Error: " . $e->getMessage(), Logger::ERROR);

// BAD - No logging
$postModel->create($data);
```

### 5. Use Middleware for Cross-Cutting Concerns

```php
// GOOD - Use middleware for authentication
'middleware' => ['Auth\Authenticate->verify'],
'controller' => 'Posts->create'

// BAD - Check authentication in controller
public function create(...) {
    if (!$this->isAuthenticated()) {
        // Authentication logic
    }
}
```

### 6. Separate Concerns

```php
// GOOD - Controller handles request/response
public function create(Request $request, Response $response) : bool {
    $data = $request->input();
    $postModel = new PostModel();
    $result = $postModel->create($data);  // Model handles DB
    $response->sendJson($result, 201);
}

// BAD - Controller handles everything
public function create(...) {
    $data = $request->input();
    $db->query("INSERT INTO posts ...");  // Database code in controller
    $response->sendJson($data, 200);
}
```

### 7. Consistent Response Format

```php
// GOOD - Consistent structure
$response->sendJson([
    'status' => 'success|error',
    'message' => 'Human readable message',
    'data' => $data
], $statusCode);

// BAD - Inconsistent formats
$response->sendJson($data, 200);
$response->sendJson(['error' => 'Failed'], 500);
$response->sendJson(['message' => 'Success'], 200);
```

### 8. Handle Edge Cases

```php
// GOOD - Check for edge cases
if (!$postId || !is_numeric($postId)) {
    $response->sendJson(['error' => 'Invalid ID'], 400);
    return false;
}

$post = $postModel->getById($postId);
if (!$post) {
    $response->sendJson(['error' => 'Not found'], 404);
    return false;
}

// BAD - Assume everything exists
$post = $postModel->getById($postId);
$response->sendJson(['data' => $post], 200);
```

---

## Common Patterns

### Pattern 1: List with Pagination

```php
public function list(Request $request, Response $response, array $middlewareData) : bool {
    $page = (int) $request->get('page', 1);
    $limit = (int) $request->get('limit', 10);
    $offset = ($page - 1) * $limit;
    
    $model = new Model();
    $items = $model->getAll($limit, $offset);
    
    $response->sendJson(['data' => $items, 'page' => $page], 200);
    return true;
}
```

### Pattern 2: Get by ID with 404 Handling

```php
public function get(Request $request, Response $response, array $middlewareData) : bool {
    $id = $request->paths(0);
    
    if (!$id || !is_numeric($id)) {
        $response->sendJson(['error' => 'Invalid ID'], 400);
        return false;
    }
    
    $model = new Model();
    $item = $model->getById($id);
    
    if (!$item) {
        $response->sendJson(['error' => 'Not found'], 404);
        return false;
    }
    
    $response->sendJson(['data' => $item], 200);
    return true;
}
```

### Pattern 3: Create with Validation

```php
public function create(Request $request, Response $response, array $middlewareData) : bool {
    $validation = Validator::validateJson($request->rawInput(), [
        'name' => 'required|string_length:3:100'
    ]);
    
    if (!empty($validation)) {
        $response->sendJson(['errors' => $validation], 422);
        return false;
    }
    
    $model = new Model();
    $id = $model->create($request->input());
    
    $response->sendJson(['id' => $id], 201);
    return true;
}
```

---

## Summary

You now understand:

- How to create HTTP controllers with Request and Response objects
- How to access different types of request data
- How to send appropriate responses with correct status codes
- How to validate input data
- How to handle errors gracefully
- How to build complete CRUD controllers
- How to work with middleware-injected data
- How to create CLI controllers for background jobs
- How to test your controllers
- Best practices for controller development

## Next Steps

1. Review [Controllers Documentation](../development/controllers.md) for detailed API reference
2. Examine existing controllers in `/src/app/Controllers/` for patterns
3. Create your own controllers following the CRUD pattern
4. Implement authentication and authorization in your routes
5. Add comprehensive error handling and logging
6. Write unit tests for your controllers

For more examples, check the built-in controllers in the `/src/app/Controllers/Development/Examples` directory.
