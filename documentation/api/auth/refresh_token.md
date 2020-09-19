# Duktig.Microservice
## RESTFul API Documentation

### Authorization / Refresh token

Version 1.0.0

Refresh the Access-Token with given Refresh token. 

In the response of [user authorization](authorize.md) request, with the access token and access token expiration time, we also getting refresh token value. 

Usually the access token has short expiration time than refresh token, so with refresh token, user is able to get new access token every time.    

Request
---

**Resource:** `/auth/refresh_token`

**Method:** `POST`

**Headers:**

```
Refresh-Token: eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTczODE0MDM0LCJpYXQiOjE1NzM4MTQwMzQsImV4cCI6MTU3MzkwMDQzNCwiYWNjb3VudCI6eyJ1c2VySWQiOjEsImZpcnN0TmFtZSI6IlN1cGVyIiwibGFzdE5hbWUiOiJBZG1pbiIsImVtYWlsIjoic3VwZXIuYWRtaW5AZHVrdGlnLmlvIiwicm9sZUlkIjoxfX0.7J1pg4vaBw9x37NOvepBP6e5yoASLQkWBobaIm593Kw
Content-Type: application/json
```

The `Refresh-Token` you received at authorization time as **refresh_token**.

**Body:**

{empty}

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "status": "OK",
    "message": "Access token regenerated successfully",
    "access_token": "eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTczODE0MDM0LCJpYXQiOjE1NzM4MTQwMzQsImV4cCI6MTU3MzkwMDQzNCwiYWNjb3VudCI6eyJ1c2VySWQiOjEsImZpcnN0TmFtZSI6IlN1cGVyIiwibGFzdE5hbWUiOiJBZG1pbiIsImVtYWlsIjoic3VwZXIuYWRtaW5AZHVrdGlnLmlvIiwicm9sZUlkIjoxfX0.7J1pg4vaBw9x37NOvepBP6e5yoASLQkWBobaIm593Kw",
    "expires_in": 1573900434
}
```

The `access_token` token from response will used to access all resources where a user authentication required.

The `expires_in` is the access token expiration time in unix format.

#### Error response:

##### Invalid token

**Status:** `401`

**Body:**

```json
{
    "status": "error",
    "message": "Invalid token"
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
