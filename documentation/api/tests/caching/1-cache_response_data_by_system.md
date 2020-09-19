# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Cache response data by System

Version 1.0.0

The Duktig system provides Response data caching by system. 

In case you want to cache a response data for future use, you just have to configure the Route to care about caching and nothing else.

In our example, the cache configured with name `ResponseDataCaching` in Application configuration and the Route will point to it by : 

```json
'cacheConfig' => 'ResponseDataCaching'
```

With only this flag in Route configuration the system will care about caching. There is no need to do caching in controller programmatically. 

Request
---

**Resource:** `/tests/get_cached?offset=0`

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

```json
{
    "status": "ok",
    "message": "This data comes directly from controller and this is not cached. If this request responded in more than 1 second, it not comes from cache.",
    "offset": "0",
    "data": [
        0,
        1,
        2,
        3,
        4
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

