# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Cache response data manually

Version 1.0.0

In Duktig system it is also possible to do response data caching manually with middleware and some code in controller. 

In our example, controller action does the caching of data. NExt time, if in middleware method a cache data is available it will response and end.    

Request
---

**Resource:** `/tests/get_custom_cached?offset=51`

**Method:** `GET`

**Headers:**

```
Content-Type:application/json
X-Dev-Auth-Key:8s79d#f798df9@78ds79f&8=79d
```

The `X-Dev-Auth-Key` value defined in application configuration.

**Body:**

{empty}

Response
---

#### Success response:

**Status:** `200`

**Body:**

No cached data

```json
{
    "status": "ok",
    "type": "not cached",
    "message": "This data comes directly from controller and this is not cached. The caching functionality works in Middleware and Controller.",
    "offset": "0",
    "data": [
        0,
        1,
        2,
        3,
        4,
        5
    ]
}
```

Cached data

```json
{
    "status": "ok",
    "type": "Cached",
    "message": "This data comes from cache.",
    "offset": "0",
    "data": [
        0,
        1,
        2,
        3,
        4,
        5
    ]
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

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document

