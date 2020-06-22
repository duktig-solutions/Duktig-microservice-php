# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Validate GET Request Data

Version 1.0.0

**Resource:** `/tests/validate_get_request_data?page=5&limit=15`

**Method:** `GET`

**Headers:**

```
X-Dev-Auth-Key:8s79d#f798df9@78ds79f&8=79d
```

The `X-Dev-Auth-Key` value defined in application configuration.

**Body:**
    No Data
    
Response
---

#### Success response:

**Status:** `200`

**Body:**

```
{
    "status": "ok",
    "data": {
        "page": "5",
        "limit": "15"
    }
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

##### Invalid GET Request Parameters

**Status:** `422`

**Body:**

```json
{
    "limit": [
        "Required int value min 5 max 25"
    ]
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
