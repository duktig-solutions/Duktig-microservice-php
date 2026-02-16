![Image](documentation/img/Logo.png "Duktig PHP Framework")

**A modern, cloud-native PHP microservices framework designed for high-performance API development with enterprise-grade architecture and built-in Docker orchestration.**


![PHP Version >= 7.4](https://img.shields.io/badge/PHP%20Version-%3E%3D%207.4-green?style=flat "PHP Version >= 7.4")
![Databases MySQL, PostgreSQL](https://img.shields.io/badge/Databases-MySQL,%20PostgreSQL-blue?style=flat "Databases MySQL, PostgreSQL")
![Pub/Sub, Message/Queue, Cache](https://img.shields.io/badge/Pub/Sub,%20Message/Queue,%20Cache-Redis-red?style=flat "Pub/Sub, Message/Queue, Cache")
![Test Coverage](https://img.shields.io/badge/coverage-94%25-brightgreen?style=flat "Test Coverage")


## Quick Start

Deploy a fully containerized development environment with a single command using Docker Compose.

All required container orchestration and configuration is included in the `docker-deployment` directory.

```shell
git clone https://github.com/duktig-solutions/Duktig-microservice-php.git
cd Duktig-microservice-php/docker-deployment
docker-compose up -d
```

Verify the deployment:

```shell
curl --request GET --url http://localhost:8088/system/ping
```

Expected response: `pong`

Proceed to the [documentation](documentation/) for tutorials and API development guides.

## Testing

Execute the comprehensive test suite:

```shell
cd ./src
./vendor/bin/phpunit --configuration phpunit.xml --testdox
```

## Why Duktig?

**Built for Modern Microservices:**
- Container-first development workflow with production-ready Dockerfiles
- Cloud-native architecture patterns and best practices
- Stateless design enabling horizontal scalability
- Zero vendor lock-in with framework-agnostic principles

**Developer Experience:**
- Minimal boilerplate with maximum productivity
- Intuitive routing and middleware pipeline
- Type-safe validation with schema definitions
- Comprehensive error handling and logging out of the box

**Production-Ready:**
- Battle-tested in enterprise environments
- 94% test coverage across unit and integration tests
- Security best practices enforced by default
- Asynchronous processing capabilities

## Architecture Highlights

**MVC-Inspired Pattern:**
Clean separation of concerns optimized for API-only services without view layer overhead.

**Middleware Pipeline:**
Composable request/response processing with support for authentication, validation, caching, and custom middleware chains.

**Event-Driven Architecture:**
Built-in Pub/Sub messaging system using Redis for asynchronous inter-service communication and event streaming.

**Repository Pattern:**
Database abstraction layer providing clean, testable data access with support for MySQL and PostgreSQL.

**Distributed Task Queue:**
Message queue implementation with worker pools for background job processing and task distribution.

## Core Features

### Containerized Development Environment

Single-command deployment with pre-configured Docker Compose orchestration:
- Isolated development containers (PHP-FPM, Nginx, MySQL/PostgreSQL, Redis)
- Volume mapping for hot-reload development workflow
- Environment-based configuration management
- Production-ready Dockerfile templates included

### Production-Ready Code Examples

Comprehensive, battle-tested examples covering:
- RESTful API CRUD operations with request validation
- JWT-based authentication and authorization
- Real-time event streaming with Pub/Sub patterns
- Distributed task queuing with worker pools
- Database transactions and rollback handling
- CLI command development with argument parsing
- Automated testing patterns (Unit and Integration tests)

### RESTful API Development

Streamlined workflow for building robust API endpoints:
- Declarative routing configuration with parameter validation
- Middleware-based request pipeline (auth, validation, caching)
- Automatic JSON response serialization
- Built-in Redis caching with route-level configuration
- OpenAPI/Swagger documentation generation support

### CLI Tools & Background Workers

Powerful command-line interface for building:
- Scheduled tasks and cron jobs with Docker-based scheduler
- Background workers for message queue processing
- Event subscription services for real-time data processing
- Database migration and seeding tools
- Custom administrative commands

### Event-Driven Messaging

**Pub/Sub Event Bus:**
Asynchronous messaging system for inter-service communication where multiple subscribers can receive broadcast events from publishers.

**Use cases:**
- Real-time notifications
- Event sourcing patterns
- Service-to-service communication
- Logging and monitoring pipelines

### Distributed Task Queue

**Message Queue with Worker Pools:**
Reliable task distribution system where each task is processed by exactly one worker with automatic retry mechanisms.

**Features:**
- Configurable retry policies with exponential backoff
- Dead letter queue for failed tasks
- Worker pool management and load balancing
- Task prioritization and scheduling

**Key difference from Pub/Sub:** Tasks are unique and processed once, whereas Pub/Sub events can be consumed by multiple subscribers simultaneously.

### Database Support

**Multi-Database Architecture:**
Production-ready libraries for MySQL and PostgreSQL with:
- Connection pooling for efficient resource management
- Prepared statement support for SQL injection prevention
- Asynchronous query execution for non-blocking operations
- Transaction management with ACID compliance
- Query builder with fluent interface
- Database migration support

### Automated Database Backup

Pre-configured Docker-based backup solution with:
- Scheduled automatic backups using cron
- Configurable retention policies
- Support for MySQL and PostgreSQL
- Compressed storage with rotation
- Restoration scripts included

### High-Performance Caching

**Redis-Backed Caching Layer:**
- Route-level automatic response caching without code changes
- Programmatic cache control for custom logic
- Cache invalidation strategies (TTL, manual, pattern-based)
- Distributed caching for multi-instance deployments
- Session storage support

### Advanced Data Validation

**Schema-Based Validation Engine:**
Declarative validation rules for complex data structures:
- Type checking (string, integer, float, boolean, array, object)
- Length and range constraints
- Pattern matching with regex support
- Nested object and array validation
- Custom validation rules
- Automatic error message generation

Example validation for API requests:
```php
$rules = [
    'email' => 'email',
    'password' => 'password:8:256',
    'age' => 'int_range:18:100',
    'tags' => 'array',
    'tags.*' => 'string_length:1:50'
];
```

### Environment Configuration Management

**Flexible Configuration System:**
- `.env` file support for local development
- Docker environment variable injection at runtime
- Configuration override hierarchy (file < compose < runtime)
- Type-safe configuration access
- Separate configs for development, staging, production
- Secrets management best practices

No more PHP-FPM environment variable struggles - configuration just works in Docker containers.

### Security Features

**Built-in Security Mechanisms:**
- Header-based API key authentication
- JWT (JSON Web Token) implementation for stateless auth
- CORS configuration support
- Request rate limiting middleware
- SQL injection prevention via prepared statements
- XSS protection on input validation
- CSRF token generation and validation
- Secure password hashing (bcrypt/argon2)

## Performance & Scalability

**Stateless Design:**
Horizontal scaling ready with no session affinity requirements.

**Connection Pooling:**
Efficient database resource management with persistent connections.

**Redis Integration:**
Sub-millisecond data retrieval for cached responses.

**Async Query Support:**
Non-blocking database operations for improved throughput.

**Minimal Footprint:**
Low memory usage per instance enables high-density deployments.

## Documentation

Comprehensive documentation available in the [`documentation/`](documentation/) directory:

- [Getting Started Guide](documentation/overview/getting-started.md)
- [CRUD Development Tutorial](documentation/tutorials/crud-development.md)
- [HTTP Workflow](documentation/development/http-workflow.md)
- [Routing Configuration](documentation/development/http-and-cli-routing.md)
- [Library Reference](documentation/Readme.md)

## Project Name

**Duktig** means skilled, capable, or hard-working in Swedish.

## Versioning

Duktig follows semantic versioning with the format `x.x.x` (e.g., `1.3.8`):

| First Number | Second Number | Third Number |
|:------------:|:-------------:|:------------:|
|  Revolution  |   Evolution   |   Bug Fix    |

- **Revolution:** Breaking changes, major architectural updates
- **Evolution:** New features, backward-compatible enhancements
- **Bug Fix:** Patches, minor improvements, security fixes

## Credits

**Author:** Duktig Solutions
**Contact:** [support@duktig.solutions](mailto:support@duktig.solutions)

[![X](https://img.shields.io/twitter/follow/DuktigS?label=News%20on%20X%20)](https://twitter.com/DuktigS)
[![LinkedIn](https://img.shields.io/badge/Company_Page-LinkedIn-blue?style=flat&logo=LinkedIn&logoColor=Blue)](https://www.linkedin.com/company/duktig-solutions)

> Project inception: April 4th, 2019
