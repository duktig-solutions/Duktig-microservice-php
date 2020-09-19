# Duktig.Microservice
## RESTFul API Documentation

### Development TEST Requests

#### Validate array from Json

Version 1.1.0

Request
---

**Resource:** `/tests/validate_array_from_json`

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
	"string1":"Hello!",
	"credit_card1":"4242424242424242",
	"credit_card2":"",
	"password1":"87s87s87s6s876876s",
	"password2":"f9#87f%FD978%7^f98",
	"password3":"A897$879@987",
	"password4":"",
	"email1":"test@example.com",
	"email2":"",
	"id1":"100",
	"id2":"",
	"digits1":"123",
	"digits2":"",
	"int_range1":10,
	"int_range2":7,
	"int_range3":6,
	"int_range4":10,
	"int_range5":"",
	"int_range6":"",
	"float_range1":45.22,
	"float_range2":10.56,
	"float_range3":34.00,
	"float_range4":10.85,
	"float_range5":5.01,
	"float_range6":"",
	"ip_address1":"192.168.0.1",
	"ip_address2":"",
	"http_host1":"localhost",
	"http_host2":"",
	"alphanumeric1":"Hello999",
	"alphanumeric2":"",
	"alpha1":"Hi",
	"alpha2":"",
    "alpha3":"TEST",
	"string_length1":"",
	"string_length2":"Hello",
	"string_length3":"ERROR",
	"string_length4":"ABCDETGYUIKIOLO",
	"string_length5":"1234",
	"string_length6":"ABTO",
	"string_length7":"",
	"url1":"http://www.example.com",
	"url2":"",
	"url5":"http://example.com/about-us",
	"url6":"http://example.com/products/keyboards/?id=99&color=red",
	"url7":"",
	"date_iso1":"1979-09-22",
	"date_iso2":"",
	"date1":"2019-06-11",
	"date2":"",
	"date3":"01/01/2010",
	"date4":"",
	"date_after1":"2020-06-09",
	"date_after2":"06/10/2019",
	"date_after3":"",
	"date_before1":"2019-06-11",
	"date_before2":"10.06.2019",
	"date_between1":"1996-06-15",
	"date_between2":"05/18/2019",
	"date_between3":"",
	"equal_to1":"ABC",
	"equal_to2":"",
    "equal_to3":"TEST",
	"not_equal_to1":"9991",
	"not_equal_to2":"",
	"one_of1":"Yes",
	"one_of2":"",
	"not_one_of1":"K",
	"not_one_of2":"",
	"array1":[1,2,3],
	"array2":["a","b"],
	"array3":[234,564,729],
	"array4":[1,2,3,4],
	"array5":[1,2,3,4,5],
	"array6":[[1,2,3], 2],
	"array7":[],
    "ids_array": [1,2,3,4],
    "ids_array1": [1,2,3],
    "ids_array2":[2,3,4],
    "ids_array3":[1,2],
    "ids_array4":[1,2, 4],
    "ids_array5":[1,2],
    "ids_array6":[1,2],
    "ids_array7":[1,2],
	"mixed_email1":"testing@example.com"
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

##### Invalid Data Structure

In case if the Key values not match with required data. 

**Status:** `422`

**Body**

```json
{
    "string1": [
        "Required value"
    ],
    "general1": [
        "Required exact values as: string1, credit_card1, credit_card2, password1, password2, password3, password4, email1, email2, id1, id2, digits1, digits2, int_range1, int_range2, int_range3, int_range4, int_range5, int_range6, float_range1, float_range2, float_range3, float_range4, float_range5, float_range6, ip_address1, ip_address2, http_host1, http_host2, alphanumeric1, alphanumeric2, alpha1, alpha2, string_length1, string_length2, string_length3, string_length4, string_length5, string_length6, string_length7, url1, url2, url5, url6, url7, date_iso1, date_iso2, date1, date2, date3, date4, date_after1, date_after2, date_after3, date_before1, date_before2, date_between1, date_between2, date_between3, equal_to1, equal_to2, not_equal_to1, not_equal_to2, one_of1, one_of2, not_one_of1, not_one_of2, array1, array2, array3, array4, array5, array6, array7, mixed_email1"
    ],
    "general3": [
        "Required no extra values: string1s. Allowed only: string1, credit_card1, credit_card2, password1, password2, password3, password4, email1, email2, id1, id2, digits1, digits2, int_range1, int_range2, int_range3, int_range4, int_range5, int_range6, float_range1, float_range2, float_range3, float_range4, float_range5, float_range6, ip_address1, ip_address2, http_host1, http_host2, alphanumeric1, alphanumeric2, alpha1, alpha2, string_length1, string_length2, string_length3, string_length4, string_length5, string_length6, string_length7, url1, url2, url5, url6, url7, date_iso1, date_iso2, date1, date2, date3, date4, date_after1, date_after2, date_after3, date_before1, date_before2, date_between1, date_between2, date_between3, equal_to1, equal_to2, not_equal_to1, not_equal_to2, one_of1, one_of2, not_one_of1, not_one_of2, array1, array2, array3, array4, array5, array6, array7, mixed_email1"
    ]
}
```

##### Invalid Data

In case if Structure is valid but Data (i.e. Email address) is not valid.  

**Status:** `422`

**Body**

```json
{
    "email1": [
        "Required valid email address"
    ]
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document

