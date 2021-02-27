![Image](documentation/Duktig_Microservice_logo.svg "Duktig.Microservice")

## Fast and Lightweight RESTFul API Microservice
     
> As Rasmus Lerdorf (PHP founder) says, **"Frameworks sucks!"**.
>
> So, let's develop and deploy a code as Lightweight as it's possible, 
> depending on your application special needs, instead of huge framework 
> with a thousands and thousands lines of code.

## Features

 - High performance Service
   - Run under Web Server such as Apache/Nginx with PHP-FPM
   - Run as a Command line interface Tool
 - Simple functionality
   - Developed for specific purposes only.
 - Flexible configuration
 - Docker container friendly
 - Security
    - JWT Based Authorization 
    - More security restrictions planned to do
 - Accounting
    - User Accounts functionality
    - User Roles functionality
 - Database access (MySQL)
    - Lightweight CRUD Library
    - Asynchronous queries
 - Redis
    - Redis Message/Queue ready
 - Caching
    - Redis
    - Memcached
 - Super Data Validation
    - Ready to handle Request Json data and more
 - Database Backup (automatic)
 - Nice HTTP Routing with middleware
         
And many more.
 
## Project Name definition

**Duktig** means skilled, capable, or hard-working - in Swedish. 
  
In phrases like `Oj, vad duktig du är!` (Wow, how skilled you are!) it's a compliment.

## Description in short

The **Duktig** project developed as RESTFul API Microservice.

It is possible to split a parts of project between hosts or run as one monolithic application.

Duktig project includes User authorization functionality with Roles/Permissions.  
Each resource in project can have allowed roles to access. For instance: Resource `/users` can be accessed only by **Super Admin** Role.

Very flexible configuration of routing and development approach can allow you to create:  
`Request -> Route -> Middleware -> Controller -> Model -> Response` in a moment.

## Version definition

The version in Duktig defined with three numbers which looks like: `x.x.x` i.e. `1.3.8`
 
|First number|Second number|Third number|
|:----:|:----:|:----:|
|Revolution|Evolution|Bug fix|
   
## References

- [Installation](documentation/install/Readme.md)
- [Project Overview](documentation/project_overview/Readme.md)
- [Development](documentation/development/Readme.md)
- [RESTFul API](documentation/api/Readme.md)

## Credits
   
- Author: `David A.` [software@duktig.dev](mailto:software@duktig.dev)

- Project Development Idea from 04 April 2019 by [Duktig Solutions](http://https://duktig.solutions/) 

> **NOTICE:** Even if this project application is under strong stress and performance testing inside docker containers, 
> We would recommend to look it as a "still under development" project.
 
