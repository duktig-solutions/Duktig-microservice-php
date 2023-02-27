# Deployment

```
NAME                         IMAGE                        COMMAND                  SERVICE                      CREATED             STATUS                   PORTS
duktig-database-mysql        duktig-database-mysql        "docker-entrypoint.s…"   duktig-database-mysql        6 minutes ago       Up 5 minutes             33060/tcp, 0.0.0.0:3308->3306/tcp
duktig-database-postgresql   duktig-database-postgresql   "docker-entrypoint.s…"   duktig-database-postgresql   5 minutes ago       Up 5 minutes (healthy)   0.0.0.0:5436->5432/tcp
duktig-database-redis        duktig-database-redis        "redis-server /etc/r…"   duktig-database-redis        5 minutes ago       Up 5 minutes             0.0.0.0:6382->6379/tcp
duktig-nginx-server          duktig-nginx-server          "/docker-entrypoint.…"   duktig-nginx-server          14 seconds ago      Up 13 seconds            0.0.0.0:8088->80/tcp
duktig-php-fpm               duktig-php-fpm               "docker-php-entrypoi…"   duktig-php-fpm               48 seconds ago      Up 47 seconds            9000/tcp
```