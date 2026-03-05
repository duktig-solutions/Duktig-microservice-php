# Duktig PHP Microservice - Development Documentation

## Overview - Project Structure

This document describes how the Duktig PHP Microservice project is organized, what each directory is responsible for, and where to place new code or documentation.

> Note: This guide reflects the current repository layout and should be updated when major structure changes are introduced.

## Table of Contents

1. [Purpose](#purpose)
2. [Top-Level Structure](#top-level-structure)
3. [Documentation Structure](#documentation-structure)
4. [Runtime Source Structure (`src/`)](#runtime-source-structure-src)
5. [Application Layer (`src/app/`)](#application-layer-srcapp)
6. [Kernel Layer (`src/kernel/`)](#kernel-layer-srckernel)
7. [Entry Points](#entry-points)
8. [Configuration and Routing](#configuration-and-routing)
9. [Tests and Dependencies](#tests-and-dependencies)
10. [Where to Place New Code](#where-to-place-new-code)
11. [Common Scenarios](#common-scenarios)
12. [Best Practices](#best-practices)
13. [Related Documentation](#related-documentation)

---

## Purpose

A clear project structure helps the team:

- find code faster
- keep responsibilities separated
- reduce architecture drift over time
- keep framework-level and application-level logic cleanly isolated

---

## Top-Level Structure

Main repository structure (simplified):

```text
/
├── Readme.md
├── documentation/
├── src/
├── docker-deployment/
└── backups/
```

### Directory intent

- `documentation/`: developer and operational documentation
- `src/`: executable code for application and kernel/framework runtime
- `docker-deployment/`: local/dev container setup and service definitions
- `backups/`: backup data storage

---

## Documentation Structure

Path: `documentation/`

```text
documentation/
├── Readme.md
├── overview/
├── installation/
├── development/
├── tutorials/
├── kernel/libraries/
├── cron-jobs/
├── references/
└── img/
```

### Section responsibilities

- `overview/`: high-level orientation and architecture understanding
- `installation/`: environment and setup instructions
- `development/`: implementation workflows and component behavior
- `tutorials/`: hands-on step-by-step development guides
- `kernel/libraries/`: API-style docs for kernel libraries
- `cron-jobs/`: scheduled operation documentation
- `references/`: constants and lookup references
- `img/`: documentation images/assets

---

## Runtime Source Structure `src/`

Path: `src/`

```text
src/
├── app/
├── cli/
├── kernel/
├── tests/
├── vendor/
├── www/
├── composer.json
├── composer.lock
└── phpunit.xml
```

### Responsibilities

- `app/`: business/application logic
- `cli/`: CLI entry and command runtime
- `kernel/`: framework/system internals and shared low-level libraries
- `tests/`: test bootstrap and unit tests
- `vendor/`: Composer dependencies
- `www/`: HTTP web entry point

---

## Application Layer `src/app/`

Path: `src/app/`

```text
src/app/
├── config/
├── Controllers/
├── Middleware/
├── Models/
├── Lib/
├── Workers/
└── log/
```

### Subdirectory responsibilities

- `config/`: route and app-level configuration
- `Controllers/`: HTTP/CLI handlers that orchestrate use-cases
- `Middleware/`: pre-controller pipeline (auth, validation, injection, caching gates)
- `Models/`: persistence and data-access logic
- `Lib/`: reusable application-level utility/services
- `Workers/`: background processing jobs/tasks
- `log/`: runtime log output

---

## Kernel Layer `src/kernel/`

Path: `src/kernel/`

```text
src/kernel/
├── Lib/
└── system/
```

### Responsibilities

- `Lib/`: shared low-level libraries (db/auth/validator/http helpers/etc.)
- `system/`: core system internals (request/response/config/bootstrap support)

Guideline:
- If logic is domain-specific, place it in `src/app/`.
- If logic is framework-generic and reusable across domains, place it in `src/kernel/`.

---

## Entry Points

### HTTP

- `src/www/index.php`

Responsibility:
- receives HTTP requests and starts request dispatch flow

### CLI

- `src/cli/exec.php`

Responsibility:
- receives CLI commands and starts command dispatch flow

---

## Configuration and Routing

Primary route/config files:

- `src/app/config/http-routes.php`
- `src/app/config/cli-routes.php`

Route definition typically binds:

- request path/command
- middleware chain
- controller method

---

## Tests and Dependencies

Key files:

- `src/phpunit.xml`: PHPUnit configuration
- `src/tests/bootstrap.php`: test bootstrap
- `src/composer.json`: dependency and autoload config
- `src/composer.lock`: exact dependency lock versions

---

## Where to Place New Code

Use this placement guide:

- New API behavior -> `src/app/Controllers/`
- Request pre-processing/auth/validation -> `src/app/Middleware/`
- DB persistence/query logic -> `src/app/Models/`
- App-specific reusable service/helper -> `src/app/Lib/`
- Background jobs and async handlers -> `src/app/Workers/`
- Framework-generic shared logic -> `src/kernel/Lib/` or `src/kernel/system/`
- New documentation -> matching section under `documentation/`

---

## Common Scenarios

### Add a new HTTP endpoint

1. Add route in `src/app/config/http-routes.php`
2. Add/update controller method in `src/app/Controllers/`
3. Add middleware in `src/app/Middleware/` if needed
4. Add/update model in `src/app/Models/` if data access needed
5. Document changes in `documentation/development/` or `documentation/tutorials/`

### Add a new CLI command

1. Add command route in `src/app/config/cli-routes.php`
2. Add controller command handler
3. Add CLI middleware if needed
4. Document command usage in development/tutorial docs

### Add reusable utility logic

- App-level utility -> `src/app/Lib/`
- Kernel-level shared utility -> `src/kernel/Lib/`

---

## Best Practices

- Keep controllers thin and orchestration-focused
- Keep middleware single-responsibility and chain-friendly
- Keep data-access logic inside models
- Avoid domain business logic in kernel layer
- Mirror namespace structure with folder structure
- Update documentation in the same PR as code structure changes

---

## Related Documentation

- [Architecture Overview](architecture-overview.md)
- [Getting Started](getting-started.md)
- [HTTP and CLI Routing](../development/http-and-cli-routing.md)
- [HTTP Workflow](../development/http-workflow.md)
- [CLI Workflow](../development/cli-workflow.md)
- [Middleware](../development/middleware.md)
- [CRUD Development](../tutorials/crud-development.md)
- [Middleware Tutorial](../tutorials/middleware.md)
