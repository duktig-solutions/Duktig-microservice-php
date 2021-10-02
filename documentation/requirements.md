# Duktig.Microservice
## Development Documentation

### Requirements

#### Web Server
It is possible to run the Duktig.Microservice with Apache or Nginx web server, 
but our tests and run cases built under Nginx web server.

#### PHP Version
Mainly Duktig.Microservice developed and requires PHP Version >= 7.0

#### PHP Classes auto-loading pattern
Duktig.Microservice Class auto loading uses PSR-4. see `composer.json`

#### Database access
Duktig.Microservice developed with nice MySQL Database CRUD functionality and requires installed MySQL Server. 

#### Redis Server Access
Duktig.Microservice developed to access to Redis server to perform `Publish/Subscribe`, `Message/Queue` and `Caching` functionality, so it's requires to have installed Redis server instance.
