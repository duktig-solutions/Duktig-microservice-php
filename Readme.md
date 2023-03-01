![Image](docs/img/logo.png "Duktig PHP Framework")

**Duktig PHP Framework is Docker friendly, Fast and Lightweight, specially written for Microservices development**


![PHP Version >= 7.4](https://img.shields.io/badge/PHP%20Version-%3E%3D%207.4-green?style=flat "PHP Version >= 7.4")
![Databases MySQL, PostgreSQL](https://img.shields.io/badge/Databases-MySQL,%20PostgreSQL-blue?style=flat "Databases MySQL, PostgreSQL")
![Pub/Sub, Message/Queue, Cache](https://img.shields.io/badge/Pub/Sub,%20Message/Queue,%20Cache-Redis-red?style=flat "Pub/Sub, Message/Queue, Cache")



## Let's start right now!

Deploy this project in your local environment with **docker-compose**, develop some features and build Docker image.

All Docker image preparations for the local environment deployment are already included in `docker-deployment` directory.

```shell   
git clone https://github.com/duktig-solutions/Duktig-microservice-php.git
cd Duktig-microservice-php/docker-deployment
docker-compose up -d
```

After successful deployment, let's check the accessibility:

```shell
curl --request GET --url http://localhost:8088/system/ping
```

You should see: `pong` response ;)

That's it! Now you can read Examples and Tutorials for future steps.

## Project Features

### Simple setup

Deploy the project in your local environment with just one command.
All required third party Docker images preparation is already included and configured,
such as Databases, Web server, etc...

### Code Examples

Includes nice and very well commented code examples to follow.

This examples includes Restful API development, CRUD, Data validation, database access, command line tools,
Message/Queue, Publish/Subscribe and many more...

### Restful API services

Very easy steps to develop a Restful API service, including - Route, Middleware, Controller, Model and final Json response.

Redis data caching mechanism is also included. It is possible to configure a Route with automatic caching for response.

### Command line tools

Create command line tools just in minutes, using routing and controller development.

Event subscriptions, Message Queue workers and other tools works in command line environment.

The Cron Docker image with examples also included in this project.

### Publish/Subscribe services

Duktig project includes Publish and Subscribe functionality which are possible to use in different scenarios.

The general purpose of this functionality is the inter-service communication,
where each command line service can subscribe for messages from other services.

### Message/Queue services with workers

The MQ functionality allows to develop workers and create tasks for them. Consumer can receive tasks and split into workers to process.

Once a task finished with fail, it can repeat until configured amount of tile.

The main difference between `Publish/Subscribe` and `Message/Queue` is that many subscribers can receive messages published by a service,
when the `Message/Queue` tasks is unique for each service. Message/Task can be received and processed by only one worker at once.

### Event driven architecture

Once we talk about microservices development, it makes sense to have a nice and easy Event driven architecture.
As mentioned before, we already have `Publish/Subscribe` mechanism bo build event driven system.

With Duktig framework, it is possible to publish events and subscribe for them using Redis.
What you have to do is to use already developed Pub/Sub Libraries.

### Databases support

Duktig framework includes database libraries for MySQL and PostgreSql.
To develop a database model, you need to follow some simple steps and inherit a base model to use.  
There is a possibility to run `Asynchronous queries` in database models.

### Database Backup

Instead of setting up a Cron Docker container from scratch, we present a ready to run solution with Automatic Database backup system.
It will allow you to back up MySQL Databases with configured time and copies.

### Data Caching

Duktig project includes a simple data caching mechanism using Redis Server.
For the Restful API development, it is possible to set automatic content caching in the Routing configuration,
without writing any line of code.

### Super Data Validation

Regular validation functions allows you to validate many types of data.
However, it is also possible to make an array of validation rules for Restful API interface
and validate a multidimensional Json data/array from incoming request.
This will allow you to build API interface quickly and secure.

### Flexible configuration

Unlike Some people, who having trouble to use environment variables in PHP-FPM Docker container,
you can define your environment variables in `.env` file and use them directly in your code.
For sure, all environment variables defined in docker-compose yaml file or defined in `docker run ..` command
will overwrite values previously defined in file.  
This will allow you to dynamically define environment variables in docker deployment time
without having trouble with hard coded configuration data.

### Security

There is a way to configure and use HEADERS based secure access key for HTTP requests.
This is a simple case, when we have to protect our Restful API interface.

However, Duktig project includes `JWT` (Json Web Token) library which you can use to develop a secured interface for your Restful API.

## Project Name definition

**Duktig** means skilled, capable, or hard-working - in Swedish.

In phrases like ***Oj, vad duktig du är!*** (Wow, how skilled you are!) it's a compliment.

## Version definition

The version in Duktig defined with three numbers which looks like: `x.x.x` i.e. `1.3.8`

| First number | Second number | Third number |
|:------------:|:-------------:|:------------:|
|  Revolution  |   Evolution   |   Bug fix    |

## Credits

Author: `Duktig Solutions` [framework@duktig.solutions](mailto:framework@duktig.solutions)

[![Twitter](https://img.shields.io/twitter/follow/DuktigS?label=News%20on%20Twitter%20)](https://twitter.com/DuktigS)

>Project Development Idea from 04 April 2019
