# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Validate multidimensional array from Json

Version 1.0.0

**Resource:** `/tests/validate_multidimensional_array_from_json`

**Method:** `POST`

**Headers:**

```
Content-Type:application/json
X-Dev-Auth-Key:8s79d#f798df9@78ds79f&8=79d
```

The `X-Dev-Auth-Key` value defined in application configuration.

**Body:**

```json
{
	"name":"David",
	"surname":"Ayvazyan",
	"email":"software@duktig.dev",
	"articles":{
		"articles_count":5,
		"detailed":{
			"last_article_date":"2019-06-05",
			"last_article_rate":4,
			"last_article_approved":"No",
			"latest_10_article_ids":[1,2,3,4,5,6,7,8,9,10],
			"latest_5_article_titles":[]
		}
	},
	"last_access":{
		"last_login_date":"2019-06-10",
		"ip_address":"192.168.0.1"
	},
	"interests":""
}
```

Response
---

#### Success response:

**Status:** `200`

**Body:**

```json
{
    "status": "ok"
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

In case if invalid data for parent item of structure.  

**Status:** `422`

**Body**

```json
{
    "last_access": {
        "last_login_date": [
            "Required valid date between 2019-04-04 - 2019-07-30 with format: Y-m-d"
        ],
        "ip_address": [
            "Required valid IP Address"
        ]
    }
}
```

In case if invalid data for child item in structure.  

**Status:** `422`

**Body**

```json
{
    "articles": {
        "detailed": {
            "last_article_date": [
                "Required valid date with format: Y-m-d"
            ]
        }
    }
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
