# Duktig.Microservice
## RESTFul API Documentation

### Users

Version 1.0.0

#### Terminate account

`Notice:` Only Users with Role **Super Admin** have access to this resource.

User account termination operation will change user status to `Terminated`. After termination, user will not be able to authorize. 

`Notice:` It is not possible to terminate own account. 

Request
---

**Resource:** `/users/{id}`

**Method:** `DELETE`

**Headers:**

```
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:** {empty} 

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "status": "OK",
    "message": "Account Terminated successfully"
}
```

#### Error response:

##### Required data not match 

**Status:** `422`

**Body:**

```json
{
    "general": [
        "Required at least one value like: firstName, lastName, email, password, phone, comment, roleId, status",
        "Required no extra values: abc. Allowed only: firstName, lastName, email, password, phone, comment, roleId, status"
    ]
}
```

##### Not able to terminate own account 

**Status:** `422`

**Body:**

```json
{
    "status": "error",
    "message": "You are not able to terminate your own account!"
}
```

##### Account not found 

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
