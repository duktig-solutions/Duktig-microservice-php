# Duktig PHP Microservice - Development Documentation

## Installation - Configuration and Environment Variables

This document explains how to configure the Duktig PHP Microservice framework using environment variables and configuration files.

## Table of Contents

1. [Overview](#overview)
2. [Environment Variables](#environment-variables)
3. [Application Configuration](#application-configuration)
4. [Database Configuration](#database-configuration)
5. [Constants](#constants)
6. [Configuration Best Practices](#configuration-best-practices)
7. [Related Documentation](#related-documentation)

---

## Overview

The Duktig PHP Framework is Docker-friendly and designed to work with environment variables. This approach allows you to:

- Separate configuration from code
- Use different settings for development, staging, and production
- Keep sensitive data (passwords, keys) out of version control
- Override configuration via Docker environment

---

## Environment Variables

### Environment File Location

Environment variables are defined in: `src/.env`

This file contains configuration values such as:
- Database connection credentials
- API keys and secure tokens
- Service endpoints
- Application settings

### How Environment Variables Work

The framework loads variables from the `.env` file automatically in both HTTP and CLI modes. Variables are stored in the `Env` static class and can be accessed throughout the application.

**Priority Order**:
1. System/Docker environment variables (highest priority)
2. `.env` file variables (lower priority)

> Note: Variables defined in Docker containers always have higher priority and will override `.env` file values.

### Example .env File

```bash
# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=UTC

# Database - MySQL
MYSQL_HOST=duktig-database-mysql
MYSQL_PORT=3306
MYSQL_DATABASE=myapp
MYSQL_USERNAME=appuser
MYSQL_PASSWORD=secure_password_here

# Database - PostgreSQL
POSTGRES_HOST=duktig-database-postgresql
POSTGRES_PORT=5432
POSTGRES_DATABASE=myapp
POSTGRES_USERNAME=appuser
POSTGRES_PASSWORD=secure_password_here

# Redis Cache
REDIS_HOST=duktig-database-redis
REDIS_PORT=6379
REDIS_PASSWORD=redis_password_here

# JWT Authentication
JWT_SECRET_KEY=your-secret-key-here
JWT_TOKEN_LIFETIME=3600

# API Keys
API_KEY=your-api-key-here
```

### Accessing Environment Variables in Code

Use the `Env` static class to retrieve environment variables:

```php
<?php
use System\Env;

// Get environment variable
$dbHost = Env::get('MYSQL_HOST');

// Get with default value if not set
$dbPort = Env::get('MYSQL_PORT', 3306);

// Check if variable exists
if (Env::has('API_KEY')) {
    $apiKey = Env::get('API_KEY');
}
```

---

## Application Configuration

### Configuration File Location

Main application configuration: `src/app/config/app.php`

This file returns an associative array containing all application settings organized by sections.

### Configuration Structure

Configuration values can be:
- **Hard-coded** - Defined directly in the configuration file
- **Environment-based** - Loaded from environment variables using `Env::get()`

**Example**:

```php
<?php
use System\Env;

return [
    'ProjectName' => 'My Microservice',
    
    'Mode' => Env::get('APP_ENV', 'development'),
    
    'DateTimezone' => Env::get('APP_TIMEZONE', 'UTC'),
    
    'Debug' => Env::get('APP_DEBUG', false)
];
```

### Configuration Sections

The configuration file is divided into logical sections. Each section groups related configuration values.

Common sections include:
- **Databases** - Database connection configurations
- **Redis** - Cache and session configurations
- **Auth** - Authentication settings
- **JWT** - JSON Web Token settings
- **CORS** - Cross-Origin Resource Sharing settings

---

## Database Configuration

### Database Configuration Structure

Database configurations are defined in the `Databases` section of `src/app/config/app.php`.

Each database connection has a unique name (instance) and contains connection parameters.

### Example: Hard-Coded Configuration

```php
<?php
'Databases' => [
    
    // Connection instance name
    'MyDatabase' => [
        'driver'   => 'MySQLi',
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => 'abc123',
        'database' => 'myapp',
        'charset'  => 'utf8mb4'
    ]
    
]
```

### Example: Environment-Based Configuration (Recommended)

```php
<?php
use System\Env;

'Databases' => [
    
    // MySQL Connection
    'MySQL_Main' => [
        'driver'   => 'MySQLi',
        'host'     => Env::get('MYSQL_HOST'),
        'port'     => Env::get('MYSQL_PORT'),
        'username' => Env::get('MYSQL_USERNAME'),
        'password' => Env::get('MYSQL_PASSWORD'),
        'database' => Env::get('MYSQL_DATABASE'),
        'charset'  => Env::get('MYSQL_CHARSET', 'utf8mb4')
    ],
    
    // PostgreSQL Connection
    'PostgreSQL_Main' => [
        'driver'   => 'PostgreSQL',
        'host'     => Env::get('POSTGRES_HOST'),
        'port'     => Env::get('POSTGRES_PORT'),
        'username' => Env::get('POSTGRES_USERNAME'),
        'password' => Env::get('POSTGRES_PASSWORD'),
        'database' => Env::get('POSTGRES_DATABASE')
    ]
    
]
```

### Multiple Database Connections

You can define multiple database connections in the same application:

```php
<?php
'Databases' => [
    'UsersDB' => [
        'host' => 'users-db.example.com',
        // ...other settings
    ],
    'ProductsDB' => [
        'host' => 'products-db.example.com',
        // ...other settings
    ],
    'AnalyticsDB' => [
        'host' => 'analytics-db.example.com',
        // ...other settings
    ]
]
```

Each model can specify which connection to use in its constructor.

### Using Database Configuration in Models

```php
<?php
namespace App\Models\Users;

use System\Config;

class User extends \Lib\Db\MySQLi {
    
    public function __construct() {
        // Load configuration for specific database instance
        $config = Config::get()['Databases']['MySQL_Main'];
        parent::__construct($config);
    }
}
```

---

## Constants

### Constants File Location

Application constants: `src/app/config/constants.php`

### Defining Constants

```php
<?php
// src/app/config/constants.php

// Application constants
define('APP_VERSION', '1.0.0');

// User status constants
define('USER_STATUS_ACTIVE', 1);
define('USER_STATUS_INACTIVE', 0);
define('USER_STATUS_BANNED', -1);

// Permission constants
define('PERMISSION_READ', 1);
define('PERMISSION_WRITE', 2);
define('PERMISSION_DELETE', 4);

// API response codes
define('API_SUCCESS', 200);
define('API_CREATED', 201);
define('API_BAD_REQUEST', 400);
define('API_UNAUTHORIZED', 401);
define('API_FORBIDDEN', 403);
define('API_NOT_FOUND', 404);
```

### Using Constants in Code

```php
<?php
// Check user status
if ($user['status'] == USER_STATUS_ACTIVE) {
    // User is active
}

// Return API response
$response->sendJson($data, API_SUCCESS);
```

See [Coding Standards](../overview/coding-standards.md) for constant naming conventions.

---

## Configuration Best Practices

### 1. Never Commit Secrets

- Add `.env` to `.gitignore`
- Use `.env.example` with dummy values as template
- Never commit passwords, API keys, or tokens

### 2. Use Environment Variables for Sensitive Data

**Good**:
```php
'password' => Env::get('DB_PASSWORD')
```

**Bad**:
```php
'password' => 'hardcoded_password_123'
```

### 3. Provide Default Values

```php
// Fallback to safe default if environment variable not set
'debug' => Env::get('APP_DEBUG', false)
```

### 4. Document Required Variables

Create `.env.example` with all required variables:

```bash
# .env.example
APP_ENV=development
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_USERNAME=root
MYSQL_PASSWORD=
```

### 5. Use Descriptive Names

**Good**:
```bash
MYSQL_PRIMARY_HOST=db1.example.com
REDIS_SESSION_HOST=redis.example.com
```

**Bad**:
```bash
DB1=db1.example.com
R1=redis.example.com
```

### 6. Group Related Variables

```bash
# MySQL Configuration
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_USERNAME=root

# Redis Configuration
REDIS_HOST=localhost
REDIS_PORT=6379
```

## Related Documentation

- [Local Development Deployment](local-dev-deployment.md) - Setting up development environment
- [Production Deployment](production-deployment.md) - Production configuration strategies
- [HTTP and CLI Routing](../development/http-and-cli-routing.md) - Route configuration details
- [Coding Standards](../overview/coding-standards.md) - Constant naming conventions
- [Getting Started](../overview/getting-started.md) - Framework overview

