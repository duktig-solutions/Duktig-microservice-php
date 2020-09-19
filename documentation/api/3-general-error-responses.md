# Duktig.Microservice
## RESTFul API Documentation

### General Error responses

Version 1.0.0

Data Validation Errors
---

#### Invalid Json data in request body

- Server not received any json data.
- Server received Json data with syntax error.

**Status:** `400` 

**Body:**

```json
  [
    "Json Error 4: Syntax error. Required valid json string"
  ]
```

#### Received data not match to required rules 

- Server received valid json data but not match with required rules.
- Server received extra data (required exact values).

**Status:** `422` 

**Body:**

```json
{
    "firstName": [
        "Required string min 2 max 10"
    ],
    "lastName": [
        "Required string min 2 max 10"
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
    "general": [
        "Required exact values as: firstName, lastName, email, password, phone"
    ]
}  
```

**Another Example of Data validation errors**

- Required at least one value from specified list
- Server received extra values described in error message and listed, what values actually allowed.

```json
{
    "general": [
        "Required at least one value like: firstName, lastName, password, phone",
        "Required no extra values: sfirstName, dpassword. Allowed only: firstName, lastName, password, phone"
    ]
}
```

#### Received data is valid, but there is another type of error.

- For example: Specified email address is already registered

**Status:** `422` 

**Body:**

```json
{
    "email": [
        "Email address is already registered"
    ]
}
``` 

Authentication errors
---

#### Unauthorized

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

#### Invalid token

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Invalid token"
}
```

#### Access-Token expired

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Access-Token Expired"
}
```

#### Forbidden

In case if user not have access to specified resource.

**Status:** `403`

**Body:**

```json
{
    "status": "error",
    "message": "Forbidden"
}
```

Server errors
---

#### Internal Server Error

In case if an error occurred in server side.

**Status:** `500`

**Body Example (App Development mode):**

If Application run in Development mode, all error details will be returned to client.

```json
{
    "status": "error",
    "type": "exception",
    "message": "exception | syntax error, unexpected '=>' (T_DOUBLE_ARROW)"
}
```

**Body Example (App Production mode):**

If Application run under Production mode, Error details will be hidden. 

```json
{
    "status": "error",
    "message": "Internal Server Error"
}
```

Other type of responses
---

#### No content

In case if request sent successfully but in server side there are no any data to response with, 
the server will return empty response with other status code.

**Status:** `204`
 
**Body:** {empty}

End of document
