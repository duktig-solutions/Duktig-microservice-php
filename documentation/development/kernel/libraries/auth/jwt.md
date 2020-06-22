# Duktig.Microservice
## Development Documentation

### Libraries

#### Auth / Jwt

The **Jwt** library provides **Javascript Web Token** functionality such as encode, decode, verify tokens and so on.

File: `kernel/lib/auth/Jwt.php`
Class: `Jwt`

##### Supported methods and algorithms

Method to process | Supported in this lib
--- | ---
Sign              | Y
Verify            | Y
iss check         | Y
sub check         | Y
aud check         | Y
exp check         | Y
nbf check         | Y
iat check         | Y
jti check         | Y
 
Algorithm to sign | Supported in this lib
--- | ---
HS256             | Y
HS384             | Y
HS512             | Y
RS256             | Y
RS384             | Y
RS512             | Y
ES256             | N
ES384             | N
ES512             | N
PS256             | N
PS384             | N
PS512             | N

##### Methods

- [encode() - Encode JWT token](#encode-jwt-token)
- [decode() - Decode JWT token](#decode-jwt-token)

###### Encode JWT token

Encode JWT token and return encoded string

`static string encode(array $payload, string $key, string $alg = 'HS256', ?array $addHeaders = [])`

Arguments:

- `array`  Payload data to token
- `string` Encryption Key
- `string` Encryption algorithm. Default: HS256
- `array`  Headers. Default: empty array 

Return value:

- `string` Encoded token string

```php
// Let's Generate the token
$token = \Lib\Auth\Jwt::encode([
    # Issuer
    'iss' => 'Duktig.microservice.iss',

    # Audience (The area where this token allowed)
    # In the future this can be used as User access area definition.
    # i.e. admin|manager|data or data (only)
    'aud' => 'Duktig.microservice.general.aud',

    # The subject of JWT
    'sub' => 'Duktig.microservice.general.sub',

    # JWT ID. Case sensitive unique identifier of the token even among different issuers.
    'jti' => 'Duktig.microservice.general.jti',
        
    # Not before (allow login not before given time)
    'nbf' => strtotime('+0 minutes'),

    # Issued at
    'iat' => time(),

    # Token expiration time
    'exp' => strtotime('+1 days'),

    # Additional payload data, in this case user account shared details
    'account' => [
        'userId' => $account['userId'],
        'firstName' => $account['firstName'],
        'lastName' => $account['lastName'],
        'email' => $account['email'],
        'roleId' => $account['roleId']
    ]],
    
    # 256-bit-secret key
    '!98fr!d8fFs#d9@f0D8%_sf9D3678Rdsdf79~'
);

// Will return a string like: eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTY2NDUxMTU1LCJpYXQiOjE1NjY0NTExNTUsImV4cCI6MTU2NjUzNzU1NSwiYWNjb3VudCI6eyJ1c2VySWQiOjEsImZpcnN0TmFtZSI6IlN1cGVyIiwibGFzdE5hbWUiOiJBZG1pbiIsImVtYWlsIjoic3VwZXIuYWRtaW5AZHVrdGlnLmlvIiwicm9sZUlkIjoxfX0.QosB9s2qcb3vO6sauAPgLjstltNc1FpltM4zFxzTxEE 
```

###### Decode JWT token

Decode JWT token and return decoded data array

**Check the payload data**

Check: | iss | aud | sub | jti | nbf | iat | exp
------ | --- | --- | --- | --- | --- | --- |-----
empty  | Y   | Y   | Y   | Y   | Y   | Y   | Y
format | N   | N   | N   | N   | Y   | Y   | Y
valid  | Y   | Y   | Y   | Y   | Y   | Y   | Y
          
`static array decode(string $jwt, string $key)`

Arguments:

- `string` Encoded JWT string
- `string` Encryption Key 

Return value:

- `array` Decoded data

```php
# Decode JWT

$jwt = 'eyJ0eXAiOiJKV1QiLCJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJEdWt0aWcuaW8uaXNzIiwiYXVkIjoiRHVrdGlnLmlvLmdlbmVyYWwuYXVkIiwic3ViIjoiRHVrdGlnLmlvLmdlbmVyYWwuc3ViIiwianRpIjoiRHVrdGlnLmlvLmdlbmVyYWwuanRpIiwibmJmIjoxNTY2NDUxMTU1LCJpYXQiOjE1NjY0NTExNTUsImV4cCI6MTU2NjUzNzU1NSwiYWNjb3VudCI6eyJ1c2VySWQiOjEsImZpcnN0TmFtZSI6IlN1cGVyIiwibGFzdE5hbWUiOiJBZG1pbiIsImVtYWlsIjoic3VwZXIuYWRtaW5AZHVrdGlnLmlvIiwicm9sZUlkIjoxfX0.QosB9s2qcb3vO6sauAPgLjstltNc1FpltM4zFxzTxEE';

# 256-bit-secret key
$secretKey = '!98fr!d8fFs#d9@f0D8%_sf9D3678Rdsdf79~';

$decodedJWT = \Lib\Auth\Jwt::decode($jwt, $secretKey);

```

In case of Successfully decoded token, the Data will look like:

```
Array
(
    [status] => ok
    [message] => JWT Decoded successfully
    [payload] => Array
        (
            [iss] => Duktig.microservice.iss
            [aud] => Duktig.microservice.general.aud
            [sub] => Duktig.microservice.general.sub
            [jti] => Duktig.microservice.general.jti
            [nbf] => 1566451155
            [iat] => 1566451155
            [exp] => 1566537555
            [account] => Array
                (
                    [userId] => 1
                    [firstName] => Jon
                    [lastName] => Smith
                    [email] => jon.smith@example.com
                    [roleId] => 1
                )

        )

)
```

If token is invalid, the returned value will look like:

```
Array
(
    [status] => error
    [message] => Invalid token
    [payload] => Array
        (
        )

)
```

***This class library contains other methods which are protected and allowed to use only in inherited class.***  

End of document
