# Duktig PHP Microservice - Development Documentation

## Requirements

This document outlines the system requirements for developing, testing, and deploying the Duktig PHP Microservice framework.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Required Software](#required-software)
3. [Optional Services](#optional-services)
4. [Database Support](#database-support)
5. [Development Tools](#development-tools)

---

## System Requirements

### Operating System

- Linux (Ubuntu 20.04+ recommended)
- macOS 10.15+
- Windows 10/11 with Docker Desktop

### Minimum Hardware

- CPU: 2 cores (4 cores recommended)
- RAM: 4 GB (8 GB recommended for production)
- Disk Space: 10 GB available

---

## Required Software

### Docker and Docker Compose

**Required** for development and production deployment.

- Docker: 20.10+ (see https://docs.docker.com/get-docker/)
- Docker Compose: 1.29+ (included with Docker Desktop)

The project is containerization-first and uses Docker for all deployment scenarios.

### PHP

**Version**: PHP 7.4+ (tested on PHP 7.4-fpm)

Core framework is developed and tested with PHP 7.4. While compatibility with PHP 8.0+ exists, primary testing and support is for PHP 7.4.

**Required PHP Extensions**:
- PDO (PHP Data Objects)
- PDO MySQL (pdo_mysql)
- PDO PostgreSQL (pdo_pgsql)
- MySQLi
- Redis (pecl)

All extensions are pre-installed in the official Docker image (`php:7.4-fpm`).

### Composer

**Required** for dependency management.

- Composer 2.0+ (see https://getcomposer.org/download/)
- Included in Docker container

Used for managing PHP dependencies and autoloading (PSR-4 standard).

### Web Server

**Default**: Nginx 1.20+

The project is configured and tested with Nginx. Alternative web servers (Apache 2.4+) may work but are not officially tested.

Nginx configuration is provided in `docker-deployment/nginx-server/`.

---

## Optional Services

### Redis

**Required for** caching, session storage, and message queues.

- Version: 7.0+ (6.0+ acceptable)
- Included in docker-compose for development

If you plan to use caching, publishing/subscription, or message queue features, Redis must be available.

### Database Servers

**Choose at least one**:

| Database | Version | Use Case |
|----------|---------|----------|
| MySQL | 8.0+ | Primary SQL database |
| PostgreSQL | 13+ | Primary SQL database |

Both are included in docker-compose. You may use one or both depending on your application needs.

---

## Database Support

The framework includes database abstraction libraries for:

- **MySQL**: Via MySQLi library and PDO
- **PostgreSQL**: Via PDO PostgreSQL and native PostgreSQL library

Both database servers are pre-configured in the development docker-compose setup. See `docker-deployment/` for configuration details.

### Database Features

- Connection pooling support
- Transaction handling
- Query utilities (SELECT, INSERT, UPDATE, DELETE)
- Prepared statements

---

## Development Tools

### Git

**Recommended** for version control.

- Git 2.20+
- Not strictly required but recommended for project management

### Code Editor/IDE

**Recommended editors**:
- JetBrains PhpStorm (full IDE, commercial)
- Visual Studio Code (lightweight, free)
- Sublime Text
- vim/Neovim

### Postman or Insomnia

**Recommended** for testing HTTP endpoints.

- Postman: https://www.postman.com/
- Insomnia: https://insomnia.rest/

---

## Architecture Support

### Class Autoloading

- Standard: PSR-4 (PHP Standards Recommendation 4)
- Configuration: See `src/composer.json`

### Design Patterns

The framework supports:
- RESTful API architecture
- CLI command architecture
- Middleware chain pattern
- Model-Controller separation (no View layer)

---

## Version Compatibility Matrix

| Component | Minimum | Recommended | Maximum |
|-----------|---------|-------------|---------|
| PHP | 7.4 | 7.4 LTS | 7.4 |
| Docker | 20.10 | Latest | Latest |
| Docker Compose | 1.29 | 2.0+ | Latest |
| MySQL | 5.7 | 8.0 | Latest |
| PostgreSQL | 12 | 13+ | Latest |
| Redis | 6.0 | 7.0+ | Latest |
| Nginx | 1.18 | 1.20+ | Latest |
| Composer | 2.0 | 2.x | 2.x |

---

## Verification Checklist

After installation, verify your environment:

```bash
# Check Docker
docker --version
docker-compose --version

# Check PHP (if running locally)
php --version

# Check Composer (if running locally)
composer --version

# Verify Docker Compose setup
docker-compose -f docker-deployment/docker-compose.yml ps
```

---

## Getting Help

- See [Getting Started](getting-started.md) for setup instructions
- Review [Local Development Deployment](../installation/local-dev-deployment.md) for environment setup
- Check [FAQ](faq.md) for common questions  

