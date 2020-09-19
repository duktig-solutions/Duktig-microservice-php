# Duktig.Microservice
## RESTFul API Documentation

### Application/System Statistics

Version 1.0.0

##### User actions

Get User actions statistics divided by time interval and user type.

Time intervals:

- Total
- Last year
- Last 3 months
- Last month
- Last 30 days
- Last 24 hours
- Last hour  

User types:

- System
- Guest
- Regular user

`Notice:` Only Users with Role **Super Admin** have access to this resource. 

Request
---

**Resource:** `/stats/user_actions`

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

```json
{
    "totalStats": {
        "Total": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last year": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last 3 months": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last 30 days": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last 24 hours": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last hour": {
            "byTypeCount": {
                "AppLogs->generateLogStats": 2,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->generateLogStats": 11.11,
                "UserActions->generateUserActionsStats": 88.89
            }
        }
    },
    "systemStats": {
        "Total": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last year": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last 3 months": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last 30 days": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last 24 hours": {
            "byTypeCount": {
                "AppLogs->archiveLogs": 2,
                "AppLogs->generateLogStats": 4,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->archiveLogs": 9.09,
                "AppLogs->generateLogStats": 18.18,
                "UserActions->generateUserActionsStats": 72.73
            }
        },
        "Last hour": {
            "byTypeCount": {
                "AppLogs->generateLogStats": 2,
                "UserActions->generateUserActionsStats": 16
            },
            "byTypePercentage": {
                "AppLogs->generateLogStats": 11.11,
                "UserActions->generateUserActionsStats": 88.89
            }
        }
    },
    "usersStats": {
        "Total": {
            "byTypeCount": {
                "AppLogs->appLogs": 3,
                "UserActions->getUserActionsStats": 9,
                "Users->getAccount": 9,
                "Users->getAccounts": 1,
                "Users->terminateAccount": 2
            },
            "byTypePercentage": {
                "AppLogs->appLogs": 12.5,
                "UserActions->getUserActionsStats": 37.5,
                "Users->getAccount": 37.5,
                "Users->getAccounts": 4.17,
                "Users->terminateAccount": 8.33
            }
        },
        "Last year": {
            "byTypeCount": {
                "AppLogs->appLogs": 3,
                "UserActions->getUserActionsStats": 9,
                "Users->getAccount": 9,
                "Users->getAccounts": 1,
                "Users->terminateAccount": 2
            },
            "byTypePercentage": {
                "AppLogs->appLogs": 12.5,
                "UserActions->getUserActionsStats": 37.5,
                "Users->getAccount": 37.5,
                "Users->getAccounts": 4.17,
                "Users->terminateAccount": 8.33
            }
        },
        "Last 3 months": {
            "byTypeCount": {
                "AppLogs->appLogs": 3,
                "UserActions->getUserActionsStats": 9,
                "Users->getAccount": 9,
                "Users->getAccounts": 1,
                "Users->terminateAccount": 2
            },
            "byTypePercentage": {
                "AppLogs->appLogs": 12.5,
                "UserActions->getUserActionsStats": 37.5,
                "Users->getAccount": 37.5,
                "Users->getAccounts": 4.17,
                "Users->terminateAccount": 8.33
            }
        },
        "Last 30 days": {
            "byTypeCount": {
                "AppLogs->appLogs": 3,
                "UserActions->getUserActionsStats": 9,
                "Users->getAccount": 9,
                "Users->getAccounts": 1,
                "Users->terminateAccount": 2
            },
            "byTypePercentage": {
                "AppLogs->appLogs": 12.5,
                "UserActions->getUserActionsStats": 37.5,
                "Users->getAccount": 37.5,
                "Users->getAccounts": 4.17,
                "Users->terminateAccount": 8.33
            }
        },
        "Last 24 hours": {
            "byTypeCount": {
                "AppLogs->appLogs": 3,
                "UserActions->getUserActionsStats": 9,
                "Users->getAccount": 9,
                "Users->getAccounts": 1,
                "Users->terminateAccount": 2
            },
            "byTypePercentage": {
                "AppLogs->appLogs": 12.5,
                "UserActions->getUserActionsStats": 37.5,
                "Users->getAccount": 37.5,
                "Users->getAccounts": 4.17,
                "Users->terminateAccount": 8.33
            }
        },
        "Last hour": {
            "byTypeCount": {
                "AppLogs->appLogs": 1,
                "UserActions->getUserActionsStats": 9,
                "Users->getAccount": 5,
                "Users->terminateAccount": 2
            },
            "byTypePercentage": {
                "AppLogs->appLogs": 5.88,
                "UserActions->getUserActionsStats": 52.94,
                "Users->getAccount": 29.41,
                "Users->terminateAccount": 11.76
            }
        }
    },
    "guestsStats": {
        "Total": {
            "byTypeCount": {
                "Auth->Authorize": 7,
                "SystemHealthCheck->ping": 2,
                "Test->downloadFile": 1,
                "Test->validateFormRequest": 1,
                "Test->validateRequestMultiDimensionalArrayFromJson": 1,
                "User->registerAccount": 1
            },
            "byTypePercentage": {
                "Auth->Authorize": 53.85,
                "SystemHealthCheck->ping": 15.38,
                "Test->downloadFile": 7.69,
                "Test->validateFormRequest": 7.69,
                "Test->validateRequestMultiDimensionalArrayFromJson": 7.69,
                "User->registerAccount": 7.69
            }
        },
        "Last year": {
            "byTypeCount": {
                "Auth->Authorize": 7,
                "SystemHealthCheck->ping": 2,
                "Test->downloadFile": 1,
                "Test->validateFormRequest": 1,
                "Test->validateRequestMultiDimensionalArrayFromJson": 1,
                "User->registerAccount": 1
            },
            "byTypePercentage": {
                "Auth->Authorize": 53.85,
                "SystemHealthCheck->ping": 15.38,
                "Test->downloadFile": 7.69,
                "Test->validateFormRequest": 7.69,
                "Test->validateRequestMultiDimensionalArrayFromJson": 7.69,
                "User->registerAccount": 7.69
            }
        },
        "Last 3 months": {
            "byTypeCount": {
                "Auth->Authorize": 7,
                "SystemHealthCheck->ping": 2,
                "Test->downloadFile": 1,
                "Test->validateFormRequest": 1,
                "Test->validateRequestMultiDimensionalArrayFromJson": 1,
                "User->registerAccount": 1
            },
            "byTypePercentage": {
                "Auth->Authorize": 53.85,
                "SystemHealthCheck->ping": 15.38,
                "Test->downloadFile": 7.69,
                "Test->validateFormRequest": 7.69,
                "Test->validateRequestMultiDimensionalArrayFromJson": 7.69,
                "User->registerAccount": 7.69
            }
        },
        "Last 30 days": {
            "byTypeCount": {
                "Auth->Authorize": 7,
                "SystemHealthCheck->ping": 2,
                "Test->downloadFile": 1,
                "Test->validateFormRequest": 1,
                "Test->validateRequestMultiDimensionalArrayFromJson": 1,
                "User->registerAccount": 1
            },
            "byTypePercentage": {
                "Auth->Authorize": 53.85,
                "SystemHealthCheck->ping": 15.38,
                "Test->downloadFile": 7.69,
                "Test->validateFormRequest": 7.69,
                "Test->validateRequestMultiDimensionalArrayFromJson": 7.69,
                "User->registerAccount": 7.69
            }
        },
        "Last 24 hours": {
            "byTypeCount": {
                "Auth->Authorize": 7,
                "SystemHealthCheck->ping": 2,
                "Test->downloadFile": 1,
                "Test->validateFormRequest": 1,
                "Test->validateRequestMultiDimensionalArrayFromJson": 1,
                "User->registerAccount": 1
            },
            "byTypePercentage": {
                "Auth->Authorize": 53.85,
                "SystemHealthCheck->ping": 15.38,
                "Test->downloadFile": 7.69,
                "Test->validateFormRequest": 7.69,
                "Test->validateRequestMultiDimensionalArrayFromJson": 7.69,
                "User->registerAccount": 7.69
            }
        },
        "Last hour": {
            "byTypeCount": [],
            "byTypePercentage": []
        }
    },
    "lastStatUpdateDate": "2019-08-13 11:29:38"
}
```

In case if data Not generated or not accessible, response will return an empty content with other status code.

**Status:** `204`

**Body:** {empty}

#### Error response:

> For more information about error responses, see [General Error Responses Document](../3-general-error-responses.md)

End of document
