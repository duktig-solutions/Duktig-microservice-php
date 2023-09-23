# Duktig PHP Microservice - Development documentation

>NOTE: This document is under development.

## Getting started

**Duktig PHP Microservice** Project provides documentation for developers to maintain a code and create a new functionality.

For this purpose we have a list of documents explaining how the project code organized and how to develop your own resources.

Let's say, this project has its own pattern and business logic. Some parts you can see is very similar to **MVC** but without "V" View files, because it is only **RESTFul API Microservice** and **CLI (Command line interface)**.

Please read the [Coding standards](overview/coding-standards.md) document before continue to develop. 
It is **Important** to keep development rules and standards in this project.

General steps to develop a new RESTFul API resource in Duktig PHP Framework listed bellow:

- Create/Configure new Route to resource
    - Create a new item in: `app/config/http-routes.php`
    - Set middleware (if required).
    - Set Controller
- Create a new middleware (if required)
- Create a model (or use any)
- Create a Controller
- Test to access and that's all!

- General steps to create a new CLI process in Duktig PHP Framework listed bellow:

- Create/Configure a new Route 
    - Create a new item in: `app/config/cli-routes.php`
    - Set middleware (if required).
    - Set Controller
- Create a new middleware (if required)
- Create a model (or use any)
- Create a Controller
- Test to access and that's all!

> You can find detailed documentation to develop an API resource or CLI process in other documents.   


