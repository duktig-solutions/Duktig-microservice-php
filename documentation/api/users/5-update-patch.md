# Duktig.Microservice
## RESTFul API Documentation

### Users

Version 1.0.0

#### Update account (parts)

Update User Account with `PATCH` request, allowed to send parts of account data.

`Notice:` Only Users with Role **Super Admin** have access to this resource. 

Request
---

**Resource:** `/users/{id}`

**Method:** `PUT`

**Headers:**

```
Content-Type:application/json
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:** 

```json
{
    "firstName": "Service",
    "lastName": "Provider",
    "email": "service.provider@duktig.dev",
    "password":"service.provider@duktig.dev",
    "phone": "+37495565003",
    "comment": "Service provider test account",
    "roleId": 3,
    "status": 1
}
```

Another example with less data

```json
{
    "phone": "+37495565008"
}
```

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "status": "OK",
    "message": "Account Patched successfully"
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

##### Invalid data 

**Status:** `422`

**Body:**

```json
{
    "email": [
        "Required valid email address or empty string"
    ]
}
```

Another example:

```json
{
    "roleId": [
        "Required value equal to 3 | 4 | 2 | 1 | 5 or empty string"
    ],
    "status": [
        "Required value equal to 1 | 0 or empty string"
    ]
}
```

##### Email address already registered 

**Status:** `422`

**Body:**

```json
{
    "email": [
        "Email address is already registered"
    ]
}
```

> For more information about error responses, see [General Error Responses Document](../3-general-error-responses.md)

End of document
