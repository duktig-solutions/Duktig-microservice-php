# Duktig.Microservice
## RESTFul API Documentation

### Getting started

Version 1.0.0

#### How to test the backend functionality?

Before start to develop any type of Frontend application to have access to **Duktig.Microservice**
backend, let's start with some awesome tools.

In this case, the **Postman** application comes to help.

You can download and install the application and start to test the Duktig.Microservice functionality.

https://www.getpostman.com/downloads/

Assume you have received test account for Duktig.Microservice before, as a separated document.  

![Postman Screen example](./images/postman-screenshot-1.png)
***Example of Postman application***  

#### Import Collection
 
After successful install of Postman you can import Duktig.Microservice Collection.
    
[Import Postman Collection](Duktig.Microservice.1.postman_collection.json)

#### Authorization

Many resources in Duktig.Microservice service requires to access only for authorized uses.

Before get Authorized, you have to access authorization resource by special auth key in header.

Example Headers:

    X-Auth-Key:abc123756%37*53f3trR3
    Content-Type:application/json 

Authorization steps listed bellow:

- Access to [/auth/token](user/2-authorize.md) resource
- Process authorization and get Access-Token, Token Expiration time and Refresh-Token
- Refresh the Access-Token with given Refresh-Token when access token expires. 
- Access to other resources using Access-Token  

#### Authentication

The Duktig.Microservice system uses JWT (Json Web Token) technology for authentication.
To access many of resources you have to provide **Access-Token** header value received at Authorization time as **access_token**.  

Example headers:

    Content-Type:application/json
    Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
 

End of document
