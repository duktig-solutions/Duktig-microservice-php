# Duktig.Microservice
## RESTFul API Documentation

### Users

Version 1.0.0

##### Get User actions 

Get user actions by Id including `Guest` and `System` users.

Special `GET` parameters required to perform this request.

`Notice:` Only Users with Role **Super Admin** have access to this resource. 

Request
---

`Notice:` There are other type of Users to get actions. 
- User Id `-2` : Guest users aka not authenticated.
- User Id `-3` : System user. 
- User Id `> 0` : Regular user.

**Resource:** `/users/{id}/actions`

**Parameters**
- `offset` - Required
- `limit` - Required
- `dateFrom` - Required (Unix timestamp)
- `dateTo` - Required (Unix timestamp) 

**Method:** `GET`

**Headers:**

```
Access-Token:eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTYxOTIxNzMwLCJpYXQiOjE1NjE5MjE3MzAsImV4cCI6MTU2MjAwODEzMCwiYWNjb3VudCI6eyJ1c2VySWQiOjEwOSwiZmlyc3ROYW1lIjoiRGF2aWQiLCJsYXN0TmFtZSI6IkF5dmF6eWFuIiwiZW1haWwiOiJ0b2tlcm5lbEBnbWFpbC5jb20iLCJpZFJvbGUiOjF9fQ.rjbkAijCx2i09dfDmpfip7mRRfRWvQo8qtREUCPX2Bg
```

The **Access-Token** token received in Authorization time as **access_token**.

**Body:** empty

Response
---

#### Success response:

**Status:** `200`

**Body:**

##### Get data for Regular User

URL Example: `/users/1/actions?offset=0&limit=10000&dateFrom=1004152688&dateTo=1565688694`

```json
[
    {
        "dateAction": "2019-08-12 11:48:38",
        "actionCode": "Users->getAccount",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 11:48:58",
        "actionCode": "Users->getAccounts",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 11:51:58",
        "actionCode": "AppLogs->appLogs",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 12:01:58",
        "actionCode": "Users->getAccount",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-13 09:19:17",
        "actionCode": "Users->terminateAccount",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-13 11:26:54",
        "actionCode": "UserActions->getUserActionsStats",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-13 12:50:39",
        "actionCode": "AppLogs->appLogs",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-13 13:30:12",
        "actionCode": "Users->getUserActions",
        "actionMessage": ""
    }    
]
```

##### Get data for Guest User

URL Example: `/users/-2/actions?offset=0&limit=10000&dateFrom=1004152688&dateTo=1565688694`

```json
[
  {
        "dateAction": "2019-08-12 11:48:47",
        "actionCode": "Test->downloadFile",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 11:48:49",
        "actionCode": "SystemHealthCheck->ping",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 11:48:51",
        "actionCode": "Auth->Authorize",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 11:48:39",
        "actionCode": "User->registerAccount",
        "actionMessage": ""
    },
    {
        "dateAction": "2019-08-12 11:48:43",
        "actionCode": "Test->validateRequestMultiDimensionalArrayFromJson",
        "actionMessage": ""
    }
]    
```

##### Get data for System User

URL Example: `/users/-3/actions?offset=0&limit=10000&dateFrom=1004152688&dateTo=1565688694`

```json
[
    {
        "dateAction": "2019-08-12 11:51:22",
        "actionCode": "AppLogs->archiveLogs",
        "actionMessage": "[\"cli/exec.php\",\"archiveLogFiles\"]"
    },
    {
        "dateAction": "2019-08-12 11:51:20",
        "actionCode": "AppLogs->generateLogStats",
        "actionMessage": "[\"cli/exec.php\",\"generateLogStats\"]"
    },
    {
        "dateAction": "2019-08-13 10:00:18",
        "actionCode": "UserActions->generateUserActionsStats",
        "actionMessage": "[\"cli/exec.php\",\"generateUserActionsStats\"]"
    }    
]
```

#### Error response:

##### Invalid `GET` Parameters 

**Status:** `422`

**Body:**

```json
{
    "offset": [
        "Required int value min 0"
    ],
    "limit": [
        "Required int value min 1"
    ],
    "dateFrom": [
        "Required valid date with format: U"
    ],
    "dateTo": [
        "Required valid date with format: U"
    ]
}
```

> For more information about error responses, see [General Error Responses Document](../3-general-error-responses.md)

End of document
