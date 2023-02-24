![Image](documentation/duktig.microservice.logo.png "Duktig.Microservice")

![PHP Version >= 7.4](https://img.shields.io/badge/PHP%20Version-%3E%3D%207.4-green)
![Databases MySQL, PostgreSQL](https://img.shields.io/badge/Databases-MySQL,%20PostgreSQL-blue)
![Pub/Sub, Cache](https://img.shields.io/badge/Pub/Sub,%20Cache-Redis-red)


[<img src="https://img.shields.io/badge/slack-@duktig-solutions/framework?style=flat&logo=slack">](https://app.slack.com/client/T04PR9UCJR0/C04QX3JGEG5/rimeto_profile/U04PNBQAHJR)
[![Twitter](https://img.shields.io/twitter/follow/DuktigS?label=News%20on%20Twitter%20)](https://twitter.com/DuktigS)


## Docker friendly! Fast and Lightweight Microservice written in PHP to support RESTFul API and CLI interfaces.

> As Rasmus Lerdorf (PHP founder) says, **"Frameworks sucks!"**.
>
> So, let's develop and deploy a simple service including your application specific requirements, 
> instead of huge framework with a thousand lines of code.

## Features

 - High performance Service
   - Running as php-fpm service with Nginx load balancer
   - Run as a Command line interface Tool
 - RESTFul API service
   - Simple to configure routing with middleware classes
 - CLI interface
   - Configure routes for CLI and call a controller to run
 - Simple functionality
   - Developed for specific purposes only.
   - Just code your controller, model, and and deploy.
 - Flexible configuration
   - Allowing to configure API and CLI routing 
 - Docker container friendly
   - Build/Run and go!
 - Database access (MySQL, PostgreSQL)
    - Lightweight CRUD Library
    - Asynchronous queries
 - Code Examples
   - Includes nice and very well commented code examples to follow.
 - Redis functionality included
    - Message/Queue ready classes uses Redis Database
    - Publish/Subscribe events (between microservices) with Redis
    - Caching with Redis functionality
 - Super Data Validation
    - Ready to handle Request Json data and more
    - Validate multidimensional arrays
 - Database Backup (automatic)
          
And many more.
 
## Project Name definition

**Duktig** means skilled, capable, or hard-working - in Swedish. 
  
In phrases like ***Oj, vad duktig du är!*** (Wow, how skilled you are!) it's a compliment.

## Description in short

The **Duktig** project developed as a RESTFul API Microservice with supported CLI functionality.

It is possible to develop multiple small services (docker containers) or run as one monolithic application.

Very flexible configuration of routing and development approach can allow you to create:  
`Request -> Route -> Middleware -> (return Cached if needed) -> Controller -> Model -> Response` in a minutes.

## Version definition

The version in Duktig defined with three numbers which looks like: `x.x.x` i.e. `1.3.8`
 
| First number | Second number | Third number |
|:------------:|:-------------:|:------------:|
|  Revolution  |   Evolution   |   Bug fix    |
   
## Documentation

- [Table of content](documentation/Readme.md)
- [Getting started](documentation/getting-started.md)
- [Requirements](documentation/requirements.md)
- [Configuration](documentation/configuration.md)
 
## Credits
   
- Author: `David A.` [davit@duktig.solutions](mailto:davit@duktig.solutions)

- Project Development Idea from 04 April 2019 by [Duktig Solutions](http://https://duktig.solutions/) 


