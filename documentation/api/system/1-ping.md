# Duktig.Microservice
## RESTFul API Documentation

### Ping to System

Version 1.0.0

This simple `GET` request will response with plain text: `pong` to ensure that the system is up and running.    

For now, this resource not requires any authentication. 

Request
---

**Resource:** `/system/ping`

**Method:** `GET`

**Headers: {empty}**

**Body: {empty}**

Response
---

#### Success response:

**Status:** `200`

**Body:**

```text
pong
```

#### Error response:

Any error response, means that the system does not functioning well.

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
