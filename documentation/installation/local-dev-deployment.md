# Duktig PHP Microservice - Development documentation

## Local deployment

This article will help you to deploy the fully working and containerized copy of Duktig PHP Framework.  
The directory `./docker-deployment` contains all required Docker image preparations for it.

As you can see in the directory structure, the project contains all Database servers and Nginx Web server for deployment.

>NOTE: Assuming, you already have up and running Docker engine and docker-compose.  

### Clone the project

Let's clone the project from GitHub and prepare for deployment 

    git clone https://github.com/duktig-solutions/Duktig-microservice-php.git
    

### Deploy in local environment 

This command will deploy the base setup of Duktig PHP Framework, which configured to run with Nginx web server,
Databases access and other.

See `./docker-deployment/docker-compose.yml`

    
    cd Duktig-microservice-php/docker-deployment
    docker-compose up -d    


After successful deployment, let's check the accessibility:

    curl --request GET --url http://localhost:8088/system/ping

You should see: `pong` response ;)

After this local environment deployment you can start your project development.

### Deployed docker containers

```
NAME                         IMAGE                        COMMAND                  SERVICE                      CREATED             STATUS                   PORTS
duktig-database-mysql        duktig-database-mysql        "docker-entrypoint.s…"   duktig-database-mysql        6 minutes ago       Up 5 minutes             33060/tcp, 0.0.0.0:3308->3306/tcp
duktig-database-postgresql   duktig-database-postgresql   "docker-entrypoint.s…"   duktig-database-postgresql   5 minutes ago       Up 5 minutes (healthy)   0.0.0.0:5436->5432/tcp
duktig-database-redis        duktig-database-redis        "redis-server /etc/r…"   duktig-database-redis        5 minutes ago       Up 5 minutes             0.0.0.0:6382->6379/tcp
duktig-nginx-server          duktig-nginx-server          "/docker-entrypoint.…"   duktig-nginx-server          14 seconds ago      Up 13 seconds            0.0.0.0:8088->80/tcp
duktig-php-fpm               duktig-php-fpm               "docker-php-entrypoi…"   duktig-php-fpm               48 seconds ago      Up 47 seconds            9000/tcp
```

