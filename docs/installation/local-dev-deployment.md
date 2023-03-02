# Local deployment

This article will help you to deploy the fully working and containerized copy of Duktig PHP Framework.  
The directory `./docker-deployment` contains all required Docker image preparations for it.

As you can see in the directory structure, the project contains all Database servers and Nginx Web server for deployment.

>NOTE: Assuming, you already have up and running Docker engine and docker-compose.  

## Clone the project

Let's clone the project from GitHub and prepare for deployment 

    git clone https://github.com/duktig-solutions/Duktig-microservice-php.git
    

## Deploy in local environment 

This command will deploy the base setup of Duktig PHP Framework, which configured to run with Nginx web server,
Databases access and other.

See `./docker-deployment/docker-compose.yml`

    
    cd Duktig-microservice-php/docker-deployment
    docker-compose up -d    


After successful deployment, let's check the accessibility:

    curl --request GET --url http://localhost:8088/system/ping

You should see: `pong` response ;)

After this local environment deployment you can start your project development.
