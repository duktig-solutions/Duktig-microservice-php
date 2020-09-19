# Duktig.Microservice
## RESTFul API Documentation

### Register an account

Version 1.0.0

Request
---

**Resource:** `/user`

**Method:** `POST`

**Headers:**

```
Content-Type: application/json
X-Auth-Key: abc123756%37*53f3trR3
```

The `X-Auth-Key` value defined in application configuration.

**Body:**

```json
{
    "firstName": "Lorito",
    "lastName": "Fibonachi",
    "email": "fibo@example.com",
    "password": "fibo@example.com.p123",
    "phone": "+498489498"
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
    "message": "Signed up successfully"
}
```

#### Error response:

##### Required values

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

##### Email already exists

**Status:** `422`

**Body:**

```json
{
    "email": [
        "Email address is already registered"
    ]
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
