# Duktig.Microservice
## RESTFul API Documentation

### Users

Version 1.0.0

##### Get user account by id

Get user account by id.

`Notice:` Only Users with Role **Super Admin** have access to this resource. 

Request
---

**Resource:** `/users/{id}`

**Method:** `GET`

**Headers:**

```
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:** empty

URL Example: `/users/2`

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "userId": 2,
    "firstName": "Regular",
    "lastName": "Admin",
    "email": "admin@duktig.dev",
    "phone": "+37495565003",
    "comment": "Regular Administrator of System",
    "dateRegistered": "2019-07-20 23:52:03",
    "dateLastUpdate": null,
    "dateLastLogin": null,
    "roleId": 2,
    "status": 1
}
```

#### Error response:

##### Invalid Id specified 

**Status:** `404`

**Body:**

```json
{
    "status": "error",
    "message": "Resource not found"
}
```

##### User not found by given id 

**Status:** `404`

**Body:**

```json
{
    "status": "error",
    "message": "Account not found"
}
```

> For more information about error responses, see [General Error Responses Document](../3-general-error-responses.md)

End of document
