# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Validate form data

Version 1.0.0

**Resource:** `/tests/validate_form_data`

**Method:** `POST`

**Headers:**

```
Content-Type:application/x-www-form-urlencoded
X-Dev-Auth-Key:8s79d#f798df9@78ds79f&8=79d
```

The `X-Dev-Auth-Key` value defined in application configuration.

**Body:**

```
name:David
email:software@duktig.dev
comment:
test_array[]:abc
test_array[]:xyz
```

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "status": "ok",
    "data": {
        "test_array": [
            "abc",
            "xyz"
        ]
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

##### Invalid Data

**Status:** `422`

**Body**

```json
{
    "email": [
        "Required valid email address"
    ]
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
