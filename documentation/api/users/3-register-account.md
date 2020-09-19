# Duktig.Microservice
## RESTFul API Documentation

### Users

Version 1.0.0

##### Register account

`Notice:` Only Users with Role **Super Admin** have access to this resource. 

Request
---

**Resource:** `/users`

**Method:** `POST`

**Headers:**

```
Content-Type:application/json
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:** 

```json
{
    "firstName": "User",
    "lastName": "Developer",
    "email": "devops@duktig.dev",
    "password": "devops@duktig.dev",
    "phone": "+37495259875",
    "roleId":"5",
    "status":0,
    "comment":"This (developer) user created by Super admin"
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
    "message": "Account created Successfully!",
    "id": 6
}
```

#### Error response:

##### Invalid data 

**Status:** `422`

**Body:**

```json
{
    "firstName": [
        "Required string min 2 max 15"
    ],
    "lastName": [
        "Required string min 2 max 20"
    ],
    "email": [
        "Required valid email address"
    ],
    "password": [
        "Required not weak Password Strength between 6 - 256 chars"
    ],
    "phone": [
        "Required string min 6 max 20"
    ],
    "roleId": [
        "Required value equal to 3 | 4 | 2 | 1 | 5"
    ],
    "status": [
        "Required value equal to 1 | 0"
    ],
    "general": [
        "Required exact values as: firstName, lastName, email, password, phone, comment, roleId, status"
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
