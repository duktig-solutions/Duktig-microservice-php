# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Response all Request Data

Version 1.0.0

**Resource:** `/tests/response_all_request_data?a=1&b=2`

**Method:** `POST`

There is two ways to sent Data Type.
This request can contain content type in headers as json or application form. 

**Headers as application/x-www-form-urlencoded:**

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
        "MIDDLEWARE_DATA": {
            "GET_Request_count": 2,
            "Some_other_data": "This is message injected in Middleware class method."
        },
        "POST_FORM_DATA": {
            "name": "David",
            "email": "software@duktig.dev",
            "comment": "",
            "test_array": [
                "abc",
                "xyz"
            ]
        },
        "POST_RAW_DATA": "{\n    \"name\": \"David\",\n    \"email\": \"software@duktig.dev\",\n    \"comment\": \"\",\n    \"test_array\": [\n        \"abc\",\n        \"xyz\"\n    ]\n}",
        "GET_DATA": {
            "a": "1",
            "b": "2"
        }
    }
}
```

**Headers as application/json:**

```
Content-Type:application/json
X-Dev-Auth-Key:8s79d#f798df9@78ds79f&8=79d
```

The `X-Dev-Auth-Key` value defined in application configuration.

**Body:**

```json
{
    "name": "David",
    "email": "software@duktig.dev",
    "comment": "",
    "test_array": [
        "abc",
        "xyz"
    ]
}
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
        "MIDDLEWARE_DATA": [],
        "POST_FORM_DATA": {
            "name": "David",
            "email": "software@duktig.dev",
            "comment": "",
            "test_array": [
                "abc",
                "xyz"
            ]
        },
        "POST_RAW_DATA": "{\n    \"name\": \"David\",\n    \"email\": \"software@duktig.dev\",\n    \"comment\": \"\",\n    \"test_array\": [\n        \"abc\",\n        \"xyz\"\n    ]\n}",
        "GET_DATA": {
            "a": "1",
            "b": "2"
        }
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

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
