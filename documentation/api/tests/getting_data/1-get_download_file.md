# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### GET/Download a file

Version 1.0.0

**Resource:** `/tests/get-file`

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
Content of file.
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
