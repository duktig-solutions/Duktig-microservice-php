# Duktig PHP Microservice - Development Documentation

> NOTE: The documentation of project is under development process.

## Table of Contents

- Overview
    - [About Project](../Readme.md)
    - [Getting Started](overview/getting-started.md)
    - [Requirements](overview/requirements.md)
    - [Unit Tests](overview/tests.md)
    - [Changelog](overview/change-log.md)
    - [Coding Standards](overview/coding-standards.md)
    - [FAQ](overview/faq.md)
    - [Architecture Overview](overview/architecture-overview.md)
    - [Project Structure](overview/project-structure.md)
    
- Installation
    - [Configuration and Environment Variables](installation/env-configuration.md)
    - [Local Development Deployment](installation/local-dev-deployment.md)
    - [Production Deployment](installation/production-deployment.md)
    - Upgrade Guide

- Development
    - [HTTP and CLI Routing](development/http-and-cli-routing.md)
    - [HTTP Workflow](development/http-workflow.md)
    - [CLI Workflow](development/cli-workflow.md)
    - [Middleware](development/middleware.md)
    - Controllers
    - Models
    - Libraries in Application Layer
    - Workers
    - Error Handling
    - Logging
    - Events Publish/Subscribe
    - Message Queue
    - Caching Strategy
    - Validation Strategy
    - Authentication and Authorization
    - API Versioning

- Kernel Libraries
    - Auth
        - [JSON Web Token (JWT)](kernel/libraries/auth/jwt.md)
        - [Password](kernel/libraries/auth/password.md)
        - Token Storage
        - Auth Key
    - Database
        - [MySQL](kernel/libraries/db/mysqli.md)
        - [MySQL Utility](kernel/libraries/db/mysqliutility.md)
        - [PostgreSQL](kernel/libraries/db/postgresql.md)
        - Query Builder
        - Migrations
    - Validation
        - [Data Validator (Valid)](kernel/libraries/valid.md)
        - [Data Structures Validator](kernel/libraries/validator.md)
    - HTTP
        - [HTTP Client](kernel/libraries/http/client.md)
        - HTTP Client Info
        - HTTP Request
        - HTTP Response
    - Caching
        - Redis
    - Events
        - Event
        - Event Publisher
    - Utilities
        - [Benchmarking](kernel/libraries/benchmarking.md)
        - Date
        - Data Generator
        - Location by IP
        - File System
        - Encryption

- Tutorials
    - [CRUD Development](tutorials/crud-development.md)
    - [Middleware](tutorials/middleware.md)
    - Authentication and Authorization
    - Validation
    - Caching
    - Events Publish/Subscribe
    - Message Queue
    - Workers
    - Database Backup
    - Building a CLI Command

- Code Examples
    - CLI (Command Line Interface)
    - Data Validation
    - Middleware Chain
    - Authentication and Authorization
    - Events Publish/Subscribe
    - Message Queue
    - Benchmarking
    - Data Caching
    - File Upload/Download

- Cron Jobs
    - [Application Logs Archiver](cron-jobs/application-logs.md)
    - [Database Backup](cron-jobs/databases-backup.md)
    - Queue Worker Runner
    - Cache Cleaner

- References
    - [Predefined Constants](references/predefined-constants.md)
    - Environment Variables Reference
    - HTTP Status and Error Codes
    - Naming Conventions

- Operations
    - Monitoring
    - Health Checks
    - Backups and Recovery
    - Incident Response

- Contributing
    - Contribution Guide
    - Pull Request Checklist
    - Documentation Style Guide
