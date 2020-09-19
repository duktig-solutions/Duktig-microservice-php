# Duktig.Microservice
## RESTFul API Documentation

### Update - PATCH

Version 1.0.0

Update User Account with `PATCH` request, allowed to send parts of account data.

Request
---

**Resource:** `/user`

**Method:** `PATCH`

**Headers:**

```
Content-Type:application/json
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:**

```json
{
    "lastName": "Fibonachike",
    "phone": "+498489495"
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
    "message": "Patched successfully"
}
```

#### Error response:

##### Required exact data 

**Status:** `422`

**Body:**

```json
{
    "general": [
        "Required at least one value like: firstName, lastName, password, phone",
        "Required no extra values: sfirstName, dpassword. Allowed only: firstName, lastName, password, phone"
    ]
}
```

> For more information about error responses, see [General Error Responses Document](../3-general-error-responses.md)

End of document
