# Duktig PHP Microservice - Development Documentation

## Overview - Architecture Overview

This document explains the high-level architecture of the Duktig PHP Microservice project. It describes core layers, request flows, and how major components work together.

> Note: This overview focuses on current project structure and implementation patterns found in `src/` and `documentation/`.

## Table of Contents

1. [Architecture Goals](#architecture-goals)
2. [High-Level Architecture](#high-level-architecture)
3. [Core Layers and Responsibilities](#core-layers-and-responsibilities)
4. [HTTP Request Flow](#http-request-flow)
5. [CLI Request Flow](#cli-request-flow)
6. [Middleware Chain](#middleware-chain)
7. [Data Access Layer](#data-access-layer)
8. [Caching, Auth, and Utilities](#caching-auth-and-utilities)
9. [Project Structure Map](#project-structure-map)
10. [Design Principles](#design-principles)
11. [Related Documentation](#related-documentation)

---

## Architecture Goals

The architecture is designed to:

- Keep business logic organized by clear concerns
- Support both HTTP and CLI execution paths
- Enable reusable middleware and libraries
- Keep transport concerns (request/response) separate from domain logic
- Provide modular extensibility for auth, validation, caching, and workers

---

## High-Level Architecture

The project follows a layered architecture with route-driven execution.

```text
Client (HTTP / CLI)
    -> Routing Configuration
        -> Middleware Chain (0..n)
            -> Controller Method
                -> Model / Library Calls
                    -> Database / Redis / External Services
                -> Response Builder
    -> Output (JSON / CLI output)
```

At runtime, routes determine which middleware and controller method execute. Middleware may validate, enrich, or short-circuit requests before controller logic runs.

---

## Core Layers and Responsibilities

### 1) Entry Layer

- HTTP entry point: `src/www/index.php`
- CLI entry point: `src/cli/exec.php`

Responsibility:
- Bootstrap framework/system
- Dispatch request to routing and execution pipeline

### 2) Routing Layer

- HTTP routes config: `src/app/config/http-routes.php`
- CLI routes config: `src/app/config/cli-routes.php`

Responsibility:
- Match incoming request/command
- Define middleware chain
- Resolve controller and method

### 3) Middleware Layer

- Location: `src/app/Middleware/`

Responsibility:
- Authentication and authorization
- Request validation
- Data injection into `$middlewareData`
- Optional response short-circuiting (for example cache hit)

### 4) Controller Layer

- Location: `src/app/Controllers/`

Responsibility:
- Orchestrate application use-cases
- Coordinate request data, middleware data, model/library calls
- Produce output via `Response`

### 5) Model / Domain Data Layer

- Location: `src/app/Models/`

Responsibility:
- Database operations and persistence logic
- Isolate query details from controllers

### 6) System and Shared Library Layer

- System core: `src/kernel/system/`
- Shared libraries: `src/kernel/Lib/`
- App libraries: `src/app/Lib/`

Responsibility:
- HTTP request/response abstraction
- Config/environment loading
- Validation, auth, DB clients, cache, utility functionality

---

## HTTP Request Flow

```text
HTTP Request
    -> `src/www/index.php`
    -> Match route in `src/app/config/http-routes.php`
    -> Execute middleware in configured order
    -> Execute mapped controller method
    -> Send JSON response
```

Typical controller method signature:

```php
public function methodName(
    \System\HTTP\Request $request,
    \System\HTTP\Response $response,
    array $middlewareData
): bool
```

Key behavior:
- Middleware can block request by sending response and returning `false`
- Successful middleware returns (possibly modified) `$middlewareData`
- Controller receives accumulated middleware data

---

## CLI Request Flow

```text
CLI Command
    -> `src/cli/exec.php`
    -> Match command route in `src/app/config/cli-routes.php`
    -> Execute middleware chain
    -> Execute mapped controller method
    -> Output result
```

CLI architecture mirrors HTTP structure, allowing shared patterns for validation, auth, and pre-processing logic.

---

## Middleware Chain

Middleware is configured per route as an ordered list:

```php
'middleware' => [
    'Namespace\\MiddlewareClass->methodOne',
    'Namespace\\AnotherMiddleware->methodTwo'
]
```

Execution characteristics:
- Order-sensitive
- Shared context via `$middlewareData`
- Supports early exit (`sendJson(...)` + `sendFinal()` + `return false`)

Common middleware categories used in this project:
- Auth (`General/Auth`, `Development/Auth`, `Accounts/Account`)
- Data injection and validation
- Caching and request enrichment

---

## Data Access Layer

Data access is handled through models and DB libraries.

- Models are grouped by domain under `src/app/Models/`
- DB utilities are available in `src/kernel/Lib/` and documented under `documentation/kernel/libraries/db/`

Current supported DB libraries in docs:
- MySQL (`mysqli`)
- MySQL Utility
- PostgreSQL

General approach:
- Controllers call model methods
- Models encapsulate query and table access logic
- Database config values are pulled from central configuration

---

## Caching, Auth, and Utilities

### Caching

- Redis-based patterns are used in middleware examples (for response caching)
- Route-level cache configuration also appears in HTTP route definitions

### Authentication

- Key-based and token-based middleware patterns are present
- JWT and Password utilities are documented under:
  - `documentation/kernel/libraries/auth/jwt.md`
  - `documentation/kernel/libraries/auth/password.md`

### Validation and Utility

- Validation libraries are documented and used in controllers/middleware
- Benchmarking and HTTP client utilities are available in kernel libraries

---

## Project Structure Map

```text
documentation/
  overview/
  installation/
  development/
  kernel/libraries/
  tutorials/
  cron-jobs/
  references/

src/
  app/
    config/
    Controllers/
    Middleware/
    Models/
    Lib/
    Workers/
  cli/
    exec.php
  kernel/
    Lib/
    system/
  www/
    index.php
```

This structure separates application-level concerns (`src/app`) from framework/system concerns (`src/kernel`).

---

## Design Principles

- Route-first execution for both HTTP and CLI
- Middleware as reusable pre-controller pipeline
- Thin controllers; data and business logic delegated to models/libraries
- Shared abstractions for request/response/config
- Extensible domain grouping (Accounts, Development examples, etc.)

---

## Related Documentation

- [Getting Started](getting-started.md)
- [HTTP and CLI Routing](../development/http-and-cli-routing.md)
- [HTTP Workflow](../development/http-workflow.md)
- [CLI Workflow](../development/cli-workflow.md)
- [Middleware](../development/middleware.md)
- [CRUD Development Tutorial](../tutorials/crud-development.md)
- [Middleware Tutorial](../tutorials/middleware.md)
