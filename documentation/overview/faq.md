# Duktig PHP Microservice - Development Documentation

## FAQ - Frequently Asked Questions

This page answers common questions about the Duktig PHP Microservice framework.

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture and Design](#architecture-and-design)
3. [Development](#development)
4. [Deployment](#deployment)
5. [Database and Data](#database-and-data)
6. [Performance](#performance)
7. [Security](#security)
8. [Troubleshooting](#troubleshooting)

---

## Project Overview

### What is Duktig PHP Microservice?

The Duktig PHP Microservice is a lightweight, container-first PHP framework designed specifically for building microservices and RESTful APIs. It provides core functionality for HTTP routing, CLI commands, middleware processing, and database abstraction while keeping the codebase minimal and focused.

### What type of project is Duktig designed for?

Duktig is primarily designed for:
- RESTful API microservices
- CLI-based command applications
- Containerized applications (Docker)
- JSON-based services
- Microservice architecture patterns

It is NOT designed as a full-stack web framework with view rendering.

### Who should use Duktig?

Duktig is ideal for developers who:
- Need lightweight API services
- Prefer explicit over implicit code
- Want clean separation of concerns
- Work with microservice architectures
- Use Docker for deployment
- Value code organization and standards

### What is the current version and status?

Current version: **1.0.0 (Stable)**  
Release date: Q4 2025  
Status: Production Ready

---

## Architecture and Design

### How does Duktig compare to Laravel or Symfony?

Unlike Laravel or Symfony, Duktig is:
- **Lighter weight** - no View layer, minimal abstraction
- **Microservice-focused** - designed for APIs and CLI, not monolithic applications
- **More explicit** - less "magic", clearer code flow
- **Container-first** - Docker deployment is primary pattern
- **Simpler** - smaller learning curve for focused use cases

Laravel and Symfony are full-stack frameworks suitable for larger applications. Duktig is optimized for microservices.

### Can I use Duktig for a monolithic application?

Yes, technically you can, but it's not recommended. Duktig is optimized for microservices and APIs. For monolithic applications with user-facing frontend, frameworks like Laravel or Symfony are better choices.

### What's the difference between HTTP and CLI execution?

**HTTP**: Requests come via Nginx/web server → PHP-FPM container → routes → middleware → controller → response  
**CLI**: Commands executed from terminal → PHP-CLI container → routes → middleware → controller → output

Both use the same routing, middleware, and controller patterns for consistency.

### Why is there no View layer?

Duktig is focused on API and backend services that return JSON data, not HTML pages. View rendering is handled by frontend applications (React, Vue, etc.) that consume the API.

---

## Development

### How do I create a new API endpoint?

1. Add route to `src/app/config/http-routes.php`
2. Create controller in `src/app/Controllers/`
3. Add middleware if needed in `src/app/Middleware/`
4. Create model in `src/app/Models/` if database access needed
5. Test the endpoint

See the [Getting Started](getting-started.md) guide and [CRUD Development Tutorial](../tutorials/crud-development.md) for detailed examples.

### How do I create a CLI command?

1. Add route to `src/app/config/cli-routes.php`
2. Create controller in `src/app/Controllers/`
3. Add middleware if needed
4. Execute from terminal or cron

See [CLI Workflow](../development/cli-workflow.md) for more details.

### What are middleware and when should I use them?

Middleware processes requests BEFORE they reach the controller. Use middleware for:
- Authentication and authorization
- Request validation
- Data injection (adding user info, etc.)
- Response caching
- Request logging
- CORS handling

See [Middleware Documentation](../development/middleware.md) and [Middleware Tutorial](../tutorials/middleware.md).

### How do I validate request data?

Use the built-in validation libraries:
- `Lib\Valid` - Single value validation (email, alpha, numeric, etc.)
- `Lib\Validator` - Data structure validation (required fields, types, etc.)

Validation can be in middleware or controllers. See [Getting Started](getting-started.md) for examples.

### How do I access database data?

Create a Model extending either:
- `\Lib\Db\MySQLi` - For MySQL
- `\Lib\Db\PostgreSQL` - For PostgreSQL

Models encapsulate database queries and provide methods like `fetch()`, `insert()`, `update()`, `delete()`.

See library documentation for database-specific methods.

### Can I use Composer packages?

Yes! Duktig uses Composer for dependency management. Add packages to `src/composer.json`:

```bash
cd src
composer require vendor/package-name
```

---

## Deployment

### What containers do I need?

Standard deployment includes:
- **PHP-FPM** - Handles HTTP requests
- **PHP-CLI** - Handles cron jobs and CLI commands
- **Nginx** - Web server / reverse proxy
- **MySQL or PostgreSQL** - Database (choose one or both)
- **Redis** (optional) - Caching and message queue

All are pre-configured in `docker-deployment/docker-compose.yml`.

### Can I deploy without Docker?

While possible, it's not recommended. Duktig is container-first designed. You would need to:
- Install PHP 7.4+
- Install database (MySQL 8.0+ or PostgreSQL 13+)
- Install Redis (for caching features)
- Configure Nginx manually
- Manage Composer dependencies

Docker deployment is much simpler.

### How do I deploy to production?

See [Production Deployment](../installation/production-deployment.md) for:
- Docker image building
- Container registry configuration
- Multi-container orchestration (Docker Swarm or Kubernetes)
- Scaling strategies
- Health checks
- Security considerations

### Can I use different databases for different models?

Yes! Define multiple database connections in `src/app/config/app.php`:

```php
'Databases' => [
    'UsersDB' => [...],
    'ProductsDB' => [...],
]
```

Models can specify which connection to use via constructor parameters.

### How do I handle database migrations?

Duktig doesn't include a migration system. You can:
1. Use external tools (Flyway, Liquibase)
2. Create SQL scripts in version control
3. Use native database tools
4. Run scripts in Docker entrypoints

---

## Database and Data

### What databases are supported?

- **MySQL** - Version 8.0+ (using MySQLi and PDO)
- **PostgreSQL** - Version 13+ (using PDO and native library)

Both have complete library support and can be used simultaneously in the same project.

### Can I use MongoDB or other NoSQL databases?

Not out-of-the-box. The framework is SQL-focused. You could:
1. Create a custom library wrapper for MongoDB
2. Use a MongoDB PHP driver (like mongodb/mongodb)
3. Make HTTP calls to MongoDB Atlas

However, SQL databases are strongly recommended for compatibility with the framework's patterns.

### How do I handle transactions?

Both MySQLi and PostgreSQL libraries support transactions:

```php
$db->startTransaction();
try {
    // Multiple operations
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}
```

See library documentation for transaction examples.

### How do I cache data?

Duktig supports Redis-based caching:
1. Via response caching middleware
2. Via explicit cache calls in code
3. Via cache keys for specific queries

See [Production Deployment](../installation/production-deployment.md) and middleware examples for caching patterns.

### How do I handle large result sets?

Use pagination:
- Add `LIMIT` and `OFFSET` to queries
- Return total count separately
- Request specific pages from client

Example:
```php
$offset = ($page - 1) * $limit;
$results = $db->fetch("SELECT * FROM items LIMIT $offset, $limit");
```

---

## Performance

### How can I improve API performance?

1. **Caching** - Cache responses and query results
2. **Indexing** - Add database indexes on frequently queried columns
3. **Pagination** - Limit result sets
4. **Connection pooling** - Reuse database connections
5. **Compression** - Enable Gzip compression in Nginx
6. **CDN** - Use CDN for static content
7. **Load balancing** - Scale with multiple containers
8. **Async jobs** - Use Workers for heavy processing

### Should I cache everything?

No. Cache strategically:
- Cache read-heavy data
- Use short TTLs for frequently changing data
- Invalidate cache when data changes
- Monitor cache hit ratios

Avoid caching:
- Frequently changing data with short TTLs
- User-specific sensitive data
- Data less than 1KB

### How do I handle long-running operations?

Use Workers or background jobs:
1. Create a worker handler
2. Queue the job (Redis/Message Queue)
3. Execute asynchronously
4. Return immediate response to client
5. Update client via polling or webhooks

## Security

### How do I authenticate users?

Use middleware for authentication:
1. API Key - Check header for valid key
2. JWT Token - Verify and decode token
3. OAuth - Integrate with OAuth provider

See [Middleware Documentation](../development/middleware.md) for authentication patterns.

### How do I protect sensitive endpoints?

Use authorization middleware:
1. Verify user identity (authentication)
2. Check user permissions/roles
3. Return 403 Forbidden if unauthorized

Example:
```php
if($user['role'] != 'admin') {
    return $response->sendJson(['error' => 'Forbidden'], 403);
}
```

### How do I validate input data?

1. Use `Lib\Valid` for simple validation
2. Use `Lib\Validator` for complex structures
3. Implement validation in middleware or controllers
4. Always validate on server side (never trust client)

### How do I prevent SQL injection?

Use prepared statements (always):

```php
$db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
```

Never use string concatenation for SQL queries.

### Should I commit `.env` to version control?

No! Environment files should NEVER be committed:
1. Add `.env` to `.gitignore`
2. Use `.env.example` with dummy values as template
3. Load actual secrets from environment variables
4. Use secret management tools in production

---

## Troubleshooting

### My route returns 404

Check:
1. Route is defined in correct config file (`http-routes.php` or `cli-routes.php`)
2. Route path matches exactly (including parameters)
3. Controller class and method exist
4. Class namespace is correct
5. Run `composer dumpautoload` if using custom classes

### Middleware blocks all requests

Check:
1. Middleware execution order (are early middleware blocking?)
2. Check middleware logs for error details
3. Verify required headers/credentials are provided
4. Test with simpler middleware first

### Database connection fails

Check:
1. Database container is running: `docker ps`
2. Correct host, port, username, password in config
3. Database exists and user has permissions
4. Check database logs: `docker logs duktig-database-mysql`

### "Class not found" error

Check:
1. Class file exists in correct location
2. Namespace matches directory structure
3. Class name matches filename (PascalCase)
4. Autoload is correct: `composer dumpautoload`

### Tests fail locally but pass in CI

Check:
1. PHP versions match
2. Required extensions installed
3. Environment variables set correctly
4. Database setup matches
5. Timezone configuration

### Docker containers won't start

Check:
1. Port conflicts with other services
2. Volume mount permissions
3. Sufficient disk space
4. Docker daemon is running
5. Container logs: `docker logs <container-name>`

---

## Getting Help

- **Documentation**: See [Documentation Index](../Readme.md)
- **Getting Started**: See [Getting Started Guide](getting-started.md)
- **Code Examples**: Check `/src/tests/Unit/` for working examples
- **Community**: Check project GitHub issues and discussions
- **Security Issues**: Report privately, do not create public issues

---

## Quick Links

- [Architecture Overview](architecture-overview.md)
- [Project Structure](project-structure.md)
- [Requirements](requirements.md)
- [Getting Started](getting-started.md)
- [Coding Standards](coding-standards.md)

