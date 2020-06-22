# Duktig.Microservice
## Documentation 

### About project

#### What is Duktig.Microservice ?
    
**Fast and Lightweight RESTFul API Framework**
     
> As Rasmus Lerdorf (PHP founder) says, **"Frameworks sucks!"**.
>
> So, let's develop and run something very Lightweight depending on special needs,
> instead of huge framework with a lot of unusable functionality.

#### Features

 - High performance Service
   - Run with Web Server such as Apache/Nginx with PHP-FPM   
   - Run as Command line interface Tool
 - Simple functionality
   - Developed for specific purposes only. 
 - Flexible configuration
 - Docker container friendly
 - Supports CLI functionality for common tools development
 - Security
    - Auth by header key (configurable)
    - Auth by JWT
 - Accounting
    - User Accounts functionality
    - User Roles functionality   
 - Database access
    - Lightweight CRUD Library
    - Asynchronous queries
    
 And many more.     
 
#### Project Name definition

**Duktig** means skilled, capable, or hard-working - in Swedish. 
  
In phrases like `Oj, vad duktig du är!` (Wow, how skilled you are!) it's a compliment.

#### Description in short

The **Duktig** project developed as RESTFul API Microservice.

It is possible to split a parts of project between hosts or run as one monolithic application.

Duktig project includes User authorization functionality with Roles/Permissions.  
Each resource in project can have allowed roles to access. For instance: Resource `/users` can be accessed only by **Super Admin** Role.

Very flexible configuration of routing and development approach can allow you to create:  
`Request -> Route -> Middleware -> Controller -> Model -> Response` in a moment.  

#### Version definition

The version in Duktig defined with three numbers which looks like: `x.x.x` i.e. `1.3.8`
 
|First number|Second number|Third number|
|:----:|:----:|:----:|
|Revolution|Evolution|Bug fix|
   
#### References

- [RESTFul API Documentation](documentation/api/Readme.md)
- [Developer Documentation](documentation/development/Readme.md)  
- [Installation](documentation/install/Readme.md)  
- [Project Overview](documentation/project_overview/Readme.md) 

#### Credits
   
- Author: `David A.` [software@duktig.dev](mailto:software@duktig.dev)

- Project Development Idea from 04 April 2019 by [Duktig Solutions](http://https://duktig.solutions/) 

> **NOTICE:** This project still under development.

End of document
