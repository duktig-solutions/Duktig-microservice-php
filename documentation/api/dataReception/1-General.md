# Duktig.Microservice
## RESTFul API Documentation

### DataReception

The General DataReception API interface allows to send Collected Data from many Systems.

Version 1.0.0

Request
---

**Resource:** `/data-reception`

**Method:** `POST`

**Headers:**

```
X-DR-Auth-Key: ds786f8d987789gd786fd867768sad6sda687
```

The `X-DR-Auth-Key` value defined in application configuration.

**Body:**

The body can contain any type of content depends on system.

For instance, The Exchange Rate System can send data to DataReception as XML, when the Hotel collected data will send data as Json.
 
Example of Data: 
 
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"> 
	<channel>
		<title>CBA Exchange Rates Feed</title>
	    <description>Exchange rates</description>
	    <link>http://www.cba.am</link>
	    <lastBuildDate>9/16/2019 12:00:00 AM</lastBuildDate>
	    <pubDate></pubDate>
	    <item>
	    	<title>USD - 1 - 476.1600</title>
            <link>http://www.cba.am/am/SitePages/ExchangeArchive.aspx</link>
		    <guid>USD9/16/2019 12:00:00 AM</guid>
		    <pubDate>9/16/2019 12:00:00 AM</pubDate>
	    </item>
	    <item>
	    	<title>GBP - 1 - 592.6300</title>
            <link>http://www.cba.am/am/SitePages/ExchangeArchive.aspx</link>
		    <guid>GBP9/16/2019 12:00:00 AM</guid>
		    <pubDate>9/16/2019 12:00:00 AM</pubDate>
	    </item>
	</channel>
</rss>
```

```json
{
	"collectingEndDate" : "2019-11-09 20:17:11",
	"collectingStartDate" : "2019-11-09 20:17:11",
	"collector" : "ExchangeRate",
	"dataReceived" : {
		"data" : [
			{
				"currency" : "USD",
				"pupDate" : "11/8/2019 12:00:00 AM",
				"rate" : "477.1700"
			},
			{
				"currency" : "GBP",
				"pupDate" : "11/8/2019 12:00:00 AM",
				"rate" : "611.2100"
			},
			{
				"currency" : "EUR",
				"pupDate" : "11/8/2019 12:00:00 AM",
				"rate" : "526.7500"
			},
			{
				"currency" : "RUB",
				"pupDate" : "11/8/2019 12:00:00 AM",
				"rate" : "7.4700"
			},
			{
				"currency" : "GEL",
				"pupDate" : "11/8/2019 12:00:00 AM",
				"rate" : "161.4400"
			}
		],
		"description" : "Get Currency Rates from Central Bank of Armenia",
		"status" : "ok"
	},
	"date" : "2019-11-09 20:17:11",
	"status" : "ok",
	"systemId" : "ExchangeRateCBA"
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
    "message": "Data Received Successfully"
}
```

#### Error response:

##### The request body is empty

**Status:** `422`

**Body:**

```json
{
    "body": [
        "Required value"
    ]
}
```

##### Unauthorized

**Status:** `422`

**Body:**

```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

More Error responses explained in [General error responses](/documentation/api/3-general-error-responses.md)

End of document
