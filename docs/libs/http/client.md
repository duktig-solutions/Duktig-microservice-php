# HTTP Client

This library allows to send request via:
 
- PHP Curl (and get detailed response)
- *nix command line curl (without response) as async operation

File: `./src/kernel/lib/Http/Client.php`
Class: `Client`

## Methods

- [sendRequest() - Send Request with PHP Curl](#send-request)
- [sendRequestAsync() - Send Request with cli curl](#send-request-async)

See also [Benchmarking](#benchmarking) 

## Send request

Send request with PHP Curl and get detailed response

`#!php static array sendRequest(string $url, string $method = 'GET', $data = '', array $headers = null)`

Arguments:

- `string` Request url
- `string` Request Method (default **GET**)
- `mixed`  Request Data
    - Array - in case if Requesting a form data, i.e. `Content-Type: multipart/form-data`
    - String - in other cases, i.e. `Content-Type: application/json`
    - NULL if there are no data
- `array` Request headers (empty by default)

Return value:

- `array` Detailed Response data
    
```
[
    [result] => '{Response data}',
    [info] => stdClass Object
            (
                [url] => http://localhost/...
                // ...
            ),
    [error] => {error info}
]

```

```php
<?php

// Send Request (Json data) and get response
$response = \Lib\Http\Client::sendRequest(
    # URL
    'http://localhost/www/index.php/tests/response_all_request_data?a=1&b=2',
    
    # Request method
    'POST',
    
    # Post data
    '{"PHP_VAL1":11,"PHP_VAL2":12}',
    
    # Headers
    [
        'X-Dev-Auth-Key' => '8s79d#f798df9@78ds79f&8=79d',
        'Content-Type' => 'application/json'
    ]
);

print_r($result);

// Send Request (Form data) and get response
$response = \Lib\Http\Client::sendRequest(
    # URL
    'http://localhost/www/index.php/tests/response_all_request_data?a=1&b=2',
    
    # Request method
    'POST',
    
    # Post data
    ["PHP_postVar1" => 'Hello!', "PHP_postVar2" => 'World!'],

    # Headers
    [
        'X-Dev-Auth-Key' => '8s79d#f798df9@78ds79f&8=79d',
        'Content-Type' => 'multipart/form-data',
    ]
);

print_r($result);
```

## Send request async

This method developed to help to send many requests as fast as possible without needing a response.

It will call system curl executable to /dev/null so the php code executable will run as fast as it's possible.

`#!phpstatic void sendRequestAsync(string $url, string $method = 'GET', $data = '', array $headers = null)`

Arguments:

- `string` Request url
- `string` Request Method (default **GET**)
- `mixed`  Request Data
    - Array - in case if Requesting a form data, i.e. `Content-Type: multipart/form-data`
    - String - in other cases, i.e. `Content-Type: application/json`
    - NULL if there are no data
- `array` Request headers (empty by default)

Return value:

- `void`

```php
<?php

// Send Request (Json data)
\Lib\Http\Client::sendRequestAsync(
    # URL
    'http://localhost/www/index.php/tests/response_all_request_data?a=1&b=2',
    
    # Request method
    'POST',
    
    # Data
    '{"Async_Val":"Testing","message":"Hello David!"}',
    
    # Headers
    [
        'X-Dev-Auth-Key' => '8s79d#f798df9@78ds79f&8=79d',
        'Content-Type' => 'application/json'
    ]
);
			
// Send Request (Form data)
\Lib\Http\Client::sendRequestAsync(
    # URL
    'http://localhost/www/index.php/tests/response_all_request_data?a=1&b=2',
    
    # Request method
    'POST',
    
    # Data
    ["AsyncPostVar1" => 'Hi', "AsyncPostVar2" => 'David!'],
    
    # Headers
    [
        'X-Dev-Auth-Key' => '8s79d#f798df9@78ds79f&8=79d',
        'Content-Type' => 'multipart/form-data',
    ]
);			
```

## Benchmarking

There is a benchmark test of methods `sendRequest()` and `sendRequestAsync()` to see the timing difference in milliseconds.

**Sending 10 Requests in loop:**

```
CLI Curl:0.027930974960327
PHP curl:0.086765050888062
```  

**Sending 100 Requests in loop:**

```
CLI Curl:0.34384799003601
PHP curl:0.6846239566803
```  

**Sending 1000 Requests in loop:**

```
CLI Curl:3.4666590690613
PHP curl:6.8786449432373
```

