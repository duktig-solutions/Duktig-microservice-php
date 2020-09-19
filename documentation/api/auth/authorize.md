# Duktig.Microservice
## RESTFul API Documentation

### Authorization

Version 1.1.0

Authorize to system and get token

Request
---

**Resource:** `/auth/token`

**Method:** `POST`

**Headers:**

```
X-Auth-Key: abc123756%37*53f3trR3
Content-Type: application/json
```

The `X-Auth-Key` value defined in application configuration.

**Body:**

```json
{
    "email":"fibo@example.com",
    "password":"fibo@example.com.p123"
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
    "message": "Signed in successfully",
    "lastLogin": "2019-11-15 11:55:37",
    "access_token": "eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTczODA2MDE2LCJpYXQiOjE1NzM4MDYwMTYsImV4cCI6MTU3Mzg5MjQxNiwiYWNjb3VudCI6eyJ1c2VySWQiOjEsImZpcnN0TmFtZSI6IlN1cGVyIiwibGFzdE5hbWUiOiJBZG1pbiIsImVtYWlsIjoic3VwZXIuYWRtaW5AZHVrdGlnLmlvIiwicm9sZUlkIjoxfX0.O1o_xTVgST5i34zdsWaccLKGU7G4NfmZeKnNV00jyZg",
    "expires_in": 1573892416,
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTczODA2MDE2LCJpYXQiOjE1NzM4MDYwMTYsImV4cCI6MTU3NjM5ODAxNiwiYWNjb3VudCI6eyJ1c2VySWQiOjF9fQ.sOIhXpWNo83ZcInPb26xDH4ZyZlb6sbaElEj_RhNkf4"
}
```

The `access_token` token from response will used to access all resources where a user authentication required.

The `expires_in` is the access token expiration time in unix format.

The `refresh_token` value will be used to get and refresh expired access token. See [Refresh token explanation](refresh_token.md).

#### Error response:

##### Required values

**Status:** `422`

**Body:**

```json
{
    "email": [
        "Required value",
        "Required valid email address"
    ],
    "password": [
        "Required value",
        "Required not weak Password Strength between 6 - 256 chars"
    ],
    "account": [
        "Required exact values as: email, password"
    ]
}
```

##### Incorrect credentials

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Incorrect credentials"
}
```

##### Account is not active

**Status:** `403`

**Body:**

```json
{
    "status": "error",
    "message": "Due to the status of your account, you are not able to access this resource"
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
