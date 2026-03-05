# Duktig PHP Microservice - Development Documentation

## Installation - Production Deployment

This guide covers deploying the Duktig PHP Microservice to production using containerization. It focuses on Docker-based deployment strategies for microservice architectures.

> Note: This guide assumes familiarity with Docker and containerization concepts. See [Local Development Deployment](local-dev-deployment.md) for development setup.

## Table of Contents

1. [Overview](#overview)
2. [Docker Image Build](#docker-image-build)
3. [Container Registry](#container-registry)
4. [Docker Compose for Production](#docker-compose-for-production)
5. [Orchestration](#orchestration)
6. [Container Networking](#container-networking)
7. [Persistent Data and Volumes](#persistent-data-and-volumes)
8. [Logging and Monitoring](#logging-and-monitoring)
9. [Security](#security)
10. [Health Checks](#health-checks)
11. [Scaling](#scaling)
12. [Troubleshooting](#troubleshooting)

---

## Overview

Production deployment of Duktig PHP Microservice involves:

- Building optimized Docker images
- Managing container orchestration
- Configuring service-to-service communication
- Implementing health checks and monitoring
- Managing secrets and configuration
- Ensuring data persistence

### Deployment Architecture

```text
External Traffic
    |
Load Balancer
    |
    +-- PHP-FPM Container (scaled)
    |
    +-- Nginx Container (reverse proxy)
    |
    +-- MySQL Container (persistent)
    |
    +-- Redis Container (cache)
    |
    +-- Worker Container (background tasks)
```

---

## Docker Image Build

### Base Dockerfile for PHP-FPM

Create `Dockerfile` in project root:

```dockerfile
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    git \
    mysql-client \
    postgresql-client \
    redis

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql

# Copy application code
COPY src/ /app/src/
COPY docker-deployment/ /app/docker-deployment/

# Set working directory
WORKDIR /app

# Copy composer files and install dependencies
COPY src/composer.json src/composer.lock /app/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /app

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:9000/ping || exit 1

EXPOSE 9000

CMD ["php-fpm"]
```

### Build and Tag Image

```bash
# Build image
docker build -t myproject/php-service:1.0.0 .

# Tag for registry
docker tag myproject/php-service:1.0.0 registry.example.com/myproject/php-service:1.0.0
```

### Nginx Container

Create `docker-deployment/nginx/Dockerfile`:

```dockerfile
FROM nginx:alpine

COPY docker-deployment/nginx/config/nginx.conf /etc/nginx/nginx.conf
COPY docker-deployment/nginx/config/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
```

---

## Container Registry

### Push Images to Registry

```bash
# Login to registry
docker login registry.example.com

# Push PHP service image
docker push registry.example.com/myproject/php-service:1.0.0

# Push Nginx image
docker push registry.example.com/myproject/nginx:1.0.0

# Tag as latest for production
docker tag registry.example.com/myproject/php-service:1.0.0 registry.example.com/myproject/php-service:latest
docker push registry.example.com/myproject/php-service:latest
```

### Registry Best Practices

- Use semantic versioning for tags (1.0.0, 1.0.1, etc.)
- Tag stable releases as "latest"
- Keep development images separate from production
- Clean up old images regularly
- Scan images for vulnerabilities before production

---

## Docker Compose for Production

### Production docker-compose.yml

```yaml
version: '3.8'

services:
  nginx:
    image: registry.example.com/myproject/nginx:1.0.0
    container_name: myproject-nginx
    restart: always
    ports:
      - "80:80"
      - "443:443"
    depends_on:
      - php-fpm
    volumes:
      - ./docker-deployment/nginx/config:/etc/nginx/conf.d
    environment:
      - NGINX_HOST=api.example.com
    networks:
      - app-network
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"

  php-fpm:
    image: registry.example.com/myproject/php-service:1.0.0
    container_name: myproject-php-fpm
    restart: always
    depends_on:
      - mysql
      - redis
    environment:
      - APP_ENV=production
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_NAME=myproject
      - DB_USERNAME=appuser
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=redis
      - REDIS_PORT=6379
    volumes:
      - app-logs:/app/src/app/log
      - app-storage:/app/storage
    networks:
      - app-network
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"

  mysql:
    image: mysql:8.0
    container_name: myproject-mysql
    restart: always
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=myproject
      - MYSQL_USER=appuser
      - MYSQL_PASSWORD=${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
      - ./docker-deployment/database-mysql/docker-entrypoint-initdb.d:/docker-entrypoint-initdb.d
    networks:
      - app-network
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"

  redis:
    image: redis:7-alpine
    container_name: myproject-redis
    restart: always
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis-data:/data
    networks:
      - app-network
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"

volumes:
  mysql-data:
  redis-data:
  app-logs:
  app-storage:

networks:
  app-network:
    driver: bridge
```

### Environment File (.env)

```bash
APP_ENV=production
DB_PASSWORD=secure_password_here
MYSQL_ROOT_PASSWORD=secure_root_password
REDIS_PASSWORD=secure_redis_password
```

### Start Services

```bash
# Start all services
docker-compose -f docker-compose.prod.yml up -d

# View logs
docker-compose -f docker-compose.prod.yml logs -f

# Scale services
docker-compose -f docker-compose.prod.yml up -d --scale php-fpm=3
```

---

## Orchestration

### Docker Swarm Deployment

Initialize swarm (on manager node):

```bash
docker swarm init
```

Create stack file `stack.yml`:

```yaml
version: '3.8'

services:
  php-fpm:
    image: registry.example.com/myproject/php-service:1.0.0
    deploy:
      replicas: 3
      update_config:
        parallelism: 1
        delay: 10s
      restart_policy:
        condition: on-failure
    environment:
      - APP_ENV=production
      - DB_HOST=mysql
    networks:
      - app-network

  nginx:
    image: registry.example.com/myproject/nginx:1.0.0
    deploy:
      replicas: 2
      placement:
        constraints: [node.role == manager]
    ports:
      - "80:80"
      - "443:443"
    networks:
      - app-network

  mysql:
    image: mysql:8.0
    deploy:
      placement:
        constraints: [node.role == manager]
    volumes:
      - mysql-data:/var/lib/mysql
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
    networks:
      - app-network

networks:
  app-network:
    driver: overlay

volumes:
  mysql-data:
```

Deploy to swarm:

```bash
# Deploy stack
docker stack deploy -c stack.yml myproject

# View services
docker stack services myproject

# Scale service
docker service scale myproject_php-fpm=5

# View service logs
docker service logs myproject_php-fpm
```

### Kubernetes Deployment (Alternative)

For Kubernetes, create deployment manifests:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: php-fpm
spec:
  replicas: 3
  selector:
    matchLabels:
      app: php-fpm
  template:
    metadata:
      labels:
        app: php-fpm
    spec:
      containers:
      - name: php-fpm
        image: registry.example.com/myproject/php-service:1.0.0
        ports:
        - containerPort: 9000
        env:
        - name: APP_ENV
          value: "production"
        - name: DB_HOST
          value: "mysql-service"
        livenessProbe:
          httpGet:
            path: /health
            port: 9000
          initialDelaySeconds: 30
          periodSeconds: 10
```

---

## Container Networking

### Service-to-Service Communication

Services communicate via service names on shared network:

```php
// In PHP code
$dbHost = getenv('DB_HOST');  // Resolves to 'mysql' container name
$redisHost = getenv('REDIS_HOST');  // Resolves to 'redis' container name
```

### Port Exposure

- Only expose public-facing ports (80, 443)
- Internal services communicate via container names
- Database ports (3306, 5432) should NOT be exposed

### DNS Resolution

Container DNS automatically resolves service names:

```bash
# From within php-fpm container
ping mysql        # Resolves to mysql container IP
ping redis        # Resolves to redis container IP
```

---

## Persistent Data and Volumes

### Volume Strategy

```yaml
volumes:
  mysql-data:
    driver: local
  redis-data:
    driver: local
  app-logs:
    driver: local
```

### Backup Volumes

```bash
# Backup MySQL data
docker run --rm -v myproject_mysql-data:/data -v $(pwd):/backup \
  busybox tar czf /backup/mysql-backup.tar.gz /data

# Restore MySQL data
docker run --rm -v myproject_mysql-data:/data -v $(pwd):/backup \
  busybox tar xzf /backup/mysql-backup.tar.gz
```

### External Volume Storage

For cloud deployments, use managed services:

- AWS EBS volumes
- Azure Managed Disks
- Google Persistent Disks
- DigitalOcean Block Storage

---

## Logging and Monitoring

### Container Logs

```bash
# View real-time logs
docker-compose -f docker-compose.prod.yml logs -f php-fpm

# View logs for specific container
docker logs myproject-php-fpm

# Stream last 100 lines
docker logs --tail 100 -f myproject-php-fpm
```

### Centralized Logging

Configure ELK stack or equivalent:

```yaml
services:
  php-fpm:
    logging:
      driver: "splunk"
      options:
        splunk-token: "${SPLUNK_TOKEN}"
        splunk-url: "https://splunk.example.com:8088"
```

### Monitoring and Alerts

- Container resource usage (CPU, memory)
- Application-level metrics
- Service health status
- Error rate tracking

---

## Security

### Image Security

- Use minimal base images (Alpine Linux)
- Scan images for vulnerabilities
- Keep images updated
- Use specific version tags (not "latest" in production)

### Secrets Management

Store sensitive data in environment or secrets manager:

```bash
# Using environment file
docker-compose --env-file .env.prod up -d

# Using Docker Secrets (Swarm)
echo "db_password" | docker secret create db_password -

# Reference in service
docker service create --secret db_password myimage
```

### Network Security

- Use overlay networks for internal communication
- Firewall external ports
- Use HTTPS/TLS for external traffic
- Implement API rate limiting

### Container Hardening

- Run containers as non-root user
- Use read-only root filesystem where possible
- Limit resource usage (CPU, memory)
- Implement resource quotas

---

## Health Checks

### Application Health Endpoint

Add to router config:

```php
'GET' => [
    '/health' => [
        'middleware' => [],
        'controller' => 'HealthCheck->status'
    ]
]
```

### Health Check Controller

```php
<?php
namespace App\Controllers;

use System\HTTP\Request;
use System\HTTP\Response;

class HealthCheck {
    
    public function status(Request $request, Response $response, array $middlewareData): bool {
        
        // Check database connection
        // Check cache connection
        // Check other dependencies
        
        $response->sendJson([
            'status' => 'healthy',
            'timestamp' => date('c'),
            'version' => '1.0.0'
        ], 200);
        
        return true;
    }
}
```

### Docker Health Check

```dockerfile
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:9000/health || exit 1
```

---

## Scaling

### Horizontal Scaling

Increase container replicas:

```bash
# Docker Compose
docker-compose up -d --scale php-fpm=5

# Docker Swarm
docker service scale myproject_php-fpm=5

# Kubernetes
kubectl scale deployment php-fpm --replicas=5
```

### Load Balancing

Nginx automatically distributes traffic:

```nginx
upstream php_backend {
    server php-fpm:9000;
}
```

### Session Management

For multiple instances, use centralized session storage:

```php
// Use Redis for sessions
$_SESSION stored in Redis (shared across containers)
```

---

## Troubleshooting

### Container Won't Start

```bash
# Check logs
docker logs myproject-php-fpm

# Inspect container
docker inspect myproject-php-fpm

# Check resource availability
docker stats
```

### Service Communication Issues

```bash
# Test network connectivity
docker exec myproject-php-fpm ping mysql

# Check DNS resolution
docker exec myproject-php-fpm nslookup mysql

# Verify network
docker network inspect app-network
```

### Performance Issues

```bash
# Check container resource usage
docker stats

# View long-running processes
docker exec myproject-php-fpm ps aux

# Check logs for errors
docker logs myproject-php-fpm | grep error
```

### Database Connection Issues

```bash
# Test MySQL connection from PHP container
docker exec myproject-php-fpm mysql -h mysql -u appuser -p

# Check MySQL logs
docker logs myproject-mysql

# Verify environment variables
docker exec myproject-php-fpm env | grep DB_
```

---

## Related Documentation

- [Local Development Deployment](local-dev-deployment.md)
- [Configuration and Environment Variables](env-configuration.md)
- [Project Structure](../overview/project-structure.md)
- [Architecture Overview](../overview/architecture-overview.md)
