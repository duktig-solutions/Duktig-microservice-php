# Requirements

## Containerization

In this containerization era, we also developed this project mainly to run in containers. 
To deploy the Duktig microservice in your local environment, it is required to install `Docker` and `docker-compose`.

See: `./docker-deployment`

## Web Server

By default, The Duktig project comes with configured `Nginx` web server (docker-compose to pull). 
Using local ./docker-deployment instructions, you can get the project up and running.    

Sure, it is possible to run the project with Apache web server too, but our tests and run cases built under Nginx web server.

## PHP Version

Mainly Duktig PHP Framework developed and tested with PHP `>=7.0`. 
However, it should also work with PHP `8.0`.

## PHP Classes auto-load pattern

Duktig PHP Framework Class auto-loading uses `PSR-4` pattern.

See: `./composer.json`

## Database server access

The project supports MySQL and PostgreSQL Libraries for database models development. 
In order to work with databases listed above, the specified server should be installed. 

Because the Duktig Microservice is container based, 
the MySQL and PostgreSQL servers configuration is already attached to docker-compose for testing purposes.
So running docker-compose you will have both database servers up and running.

## Redis Server Access

Duktig PHP Framework includes `Publish/Subscribe`, `Message/Queue` 
and `Caching` functionality using Redis server. 
In order to use any of this, the Redis server should be installed.
The local ./docker-compose includes Redis server configured to start in deployment time.  

