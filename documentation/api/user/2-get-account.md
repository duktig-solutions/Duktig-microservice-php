# Duktig.Microservice
## RESTFul API Documentation

### Get account

Version 1.0.0

Get User account, requires authorized token 

Request
---

**Resource:** `/user`

**Method:** `GET`

**Headers:**

```
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:** None

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "userId": 109,
    "firstName": "Lorito",
    "lastName": "Fibonachi",
    "email": "fibo@example.com",
    "phone": "+498489498",
    "pinCode": null,
    "dateRegistered": "2019-06-02 12:07:04",
    "dateLastUpdate": "2019-07-15 23:19:15",
    "dateLastLogin": "2019-07-15 23:19:15"
}
```

#### Error response:

##### Unauthorized

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

##### Invalid token

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Invalid token"
}
```

##### Access-Token expired

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Access-Token Expired"
}
```

> Notice: In next documents to get information about general error responses, see [Authorization Errors in General Error Responses Document](../3-general-error-responses.md)  

End of document
