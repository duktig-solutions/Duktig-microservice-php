![Image](documentation/duktig.microservice.logo.png "Duktig.Microservice")

## Fast and Lightweight Docker friendly Microservice written in PHP to support RESTFul API and CLI interfaces.

> As Rasmus Lerdorf (PHP founder) says, **"Frameworks sucks!"**.
>
> So, let's develop and deploy a code as Lightweight as it's possible, 
> depending on your application special needs, instead of huge framework 
> with a thousands and thousands lines of code.

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
   - Just develop your controller and model, so you're ready.
 - Flexible configuration
   - Allowing to configure API and CLI routing 
 - Docker container friendly
   - Build/Run and go!
 - Database access (MySQL)
    - Lightweight CRUD Library
    - Asynchronous queries
 - Code Examples
   - Included nice and very well commented code examples to follow.
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
  
In phrases like `Oj, vad duktig du är!` (Wow, how skilled you are!) it's a compliment.

## Description in short

The **Duktig** project developed as a RESTFul API Microservice with supported CLI functionality.

It is possible to split a parts of project between hosts or run as one monolithic application.

Very flexible configuration of routing and development approach can allow you to create:  
`Request -> Route -> Middleware -> (return Cached if needed) -> Controller -> Model -> Response` in a moment.

## Version definition

The version in Duktig defined with three numbers which looks like: `x.x.x` i.e. `1.3.8`
 
|First number|Second number|Third number|
|:----:|:----:|:----:|
|Revolution|Evolution|Bug fix|
   
## Documentation

- [Table of content](documentation/Readme.md)
- [Getting started](documentation/getting-started.md)
- [Requirements](documentation/requirements.md)
- [Configuration](documentation/configuration.md)
 
## Credits
   
- Author: `David A.` [software@duktig.dev](mailto:software@duktig.dev)

- Project Development Idea from 04 April 2019 by [Duktig Solutions](http://https://duktig.solutions/) 

> **NOTICE:** Even if this project application is under strong stress and performance testing inside docker containers, 
> We would recommend to look it as a "still under development" project.
