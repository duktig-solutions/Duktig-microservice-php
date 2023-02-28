# Valid

Version 1.1.0

Data validation class library.

> You can use this library to validate data by specified methods.  
If you want to validate data structures with multiple rules, refer to - [Validator library](validator.md).

File: `kernel/lib/Valid.php`
Class: `Valid`

##### Methods

- [Duktig.Microservice](#duktigmicroservice)
  - [Development Documentation](#development-documentation)
    - [Libraries](#libraries)
      - [Valid](#valid)
        - [Methods](#methods)
          - [Alphabetical value](#alphabetical-value)
          - [Alphanumeric value](#alphanumeric-value)
          - [Credit card](#credit-card)
          - [Date](#date)
          - [Date between](#date-between)
          - [Date equal or after](#date-equal-or-after)
          - [Date equal or before](#date-equal-or-before)
          - [Date ISO](#date-iso)
          - [Date-Time ISO](#date-time-iso)
          - [Digits](#digits)
          - [Email](#email)
          - [Float range](#float-range)
          - [HTTP host](#http-host)
          - [ID](#id)
          - [Int range](#int-range)
          - [IP Address](#ip-address)
          - [Json string](#json-string)
          - [Password strength](#password-strength)
          - [String length](#string-length)
          - [URL](#url)
          - [Latitude](#latitude)
          - [Longitude](#longitude)
           
###### Alphabetical value

Check if the value is alphabetical (a-z).

`static bool alpha(mixed $data)`

Arguments:

- `mixed`  Data to check

Return value:

- `bool` True in case if alphabetical value
- `bool` False in all other cases

```php
// this will return true
$isAlpha = \Lib\Valid::alpha('hello');

// this will return false
$isAlpha = \Lib\Valid::alpha('hello ! 234');
```

###### Alphanumeric value

Check if the value is alphanumeric a-z - 0-9

`static bool alphaNumeric(mixed $data)`

Arguments:

- `mixed`  Data to check

Return value:

- `bool` True in case if alphanumeric value
- `bool` False in all other cases

```php
// this will return true 
$isAlphaNumeric = \Lib\Valid::alphaNumeric('hello123');

// this will return false 
$isAlphaNumeric = \Lib\Valid::alphaNumeric('hello123 @ _ !');
```

###### Credit card

Check if the value is valid credit card

`static mixed creditCard(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `string` Credit card type: 
    - Visa: visa 
    - Mastercard: mastercard
    - American Express: amex
    - Discover: discover
    - Diners Club International: diners
    
- `bool` False in case if not valid credit card number

```php
// this will return true 
$isValidCard = \Lib\Valid::creditCard('4242424242424242');

// this will return false 
$isValidCard = \Lib\Valid::creditCard('hello');
```

###### Date

Check if the value is valid date with given format

`static bool date(mixed $data, string $format = 'Y-m-d')`

Arguments:

- `mixed` Data to check
- `string` Format = 'Y-m-d' 

Return value:

- `bool` True if valid 
- `bool` False if invalid

```php
// this will return true 
$isValidDate = \Lib\Valid::date('2019-08-27', 'Y-m-d');
$isValidDate = \Lib\Valid::date('08/27/2019', 'm/d/Y');

// this will return false 
$isValidDate = \Lib\Valid::date('1259drte45');
```

###### Date between

Check if the date is between or equal to range of dates

`static bool dateBetween($data, string $startDate, string $endDate, string $format = 'Y-m-d')`

Arguments:

- `mixed` Data to check
- `string` Start date
- `string` End date
- `string` Format = 'Y-m-d' 

Return value:

- `bool` True if the given date is between start and end dates 
- `bool` False if date is invalid or out of range 

```php
// this will return true 
$isDateBetween = \Lib\Valid::dateBetween('2019-08-27', '2019-08-26', '2019-08-28', 'Y-m-d');

// If the date is equal to start or end date it is also will return True
$isDateBetween = \Lib\Valid::dateBetween('08/27/2019', '08/27/2019', '08/27/2019', 'm/d/Y');

// this will return false because of invalid date 
$isDateBetween = \Lib\Valid::dateBetween('abc', '2019-08-26', '2019-08-28', 'Y-m-d');

// this will return false because the given date is out of range
$isDateBetween = \Lib\Valid::dateBetween('2019-08-30', '2019-08-26', '2019-08-28', 'Y-m-d');

```

###### Date equal or after

Check if the given date is equal or after 

`static bool dateEqualOrAfter(mixed $data, string $checkDate, string $format = 'Y-m-d')`

Arguments:

- `mixed` Data to check
- `string` Date to check
- `string` Format = 'Y-m-d' 

Return value:

- `bool` True if the given date is equal or after date 
- `bool` False if the given date is invalid or before date 

```php
// this will return true 
$isDateEqualOrAfter = \Lib\Valid::dateEqualOrAfter('2019-08-27', '2019-08-26', 'Y-m-d');

// If the date is equal or after date it is also will return True
$isDateEqualOrAfter = \Lib\Valid::dateEqualOrAfter('08/27/2019', '08/27/2019', 'm/d/Y');

// this will return false because of invalid date 
$isDateEqualOrAfter = \Lib\Valid::dateEqualOrAfter('abc', '2019-08-26', 'Y-m-d');

// this will return false because the given date is before date
$isDateEqualOrAfter = \Lib\Valid::dateEqualOrAfter('2019-08-27', '2019-08-28', 'Y-m-d');

```

###### Date equal or before

Check if the given date is equal or before 

`static bool dateEqualOrBefore(mixed $data, string $checkDate, string $format = 'Y-m-d')`

Arguments:

- `mixed` Data to check
- `string` Date to check
- `string` Format = 'Y-m-d' 

Return value:

- `bool` True if the given date is equal or before date 
- `bool` False if the given date is invalid or after date 

```php
// this will return true 
$isDateEqualOrBefore = \Lib\Valid::dateEqualOrBefore('2019-08-27', '2019-08-26', 'Y-m-d');

// If the date is equal or before date it is also will return True
$isDateEqualOrBefore = \Lib\Valid::dateEqualOrBefore('08/27/2019', '08/27/2019', 'm/d/Y');

// this will return false because of invalid date 
$isDateEqualOrBefore = \Lib\Valid::dateEqualOrBefore('abc', '2019-08-26', 'Y-m-d');

// this will return false because the given date is after date
$isDateEqualOrBefore = \Lib\Valid::dateEqualOrBefore('2019-08-27', '2019-08-26', 'Y-m-d');

```

###### Date ISO

Check if the given date is valid and ISO standard 8601: Y-m-d

`static bool dateIso(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given date is valid and ISO standard Y-m-d 
- `bool` False if the given date is invalid or not ISO standard

```php
// this will return true 
$isDateISO = \Lib\Valid::dateIso('2019-08-27');

// this will return false because of invalid date 
$isDateISO = \Lib\Valid::dateIso(12554747847847);

// this will return false because the given date is not ISO standard
$isDateISO = \Lib\Valid::dateIso('08/27/2019');

```

###### Date-Time ISO

Check if the given date and time is valid and ISO standard 8601: Y-m-d H:i:s

`static bool dateTimeIso(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given date and time is valid and ISO standard 8601 Y-m-d H:i:s
- `bool` False if the given date and time is invalid or not ISO standard 8601

```php
// this will return true 
$isDateTimeISO = \Lib\Valid::dateTimeIso('2023-02-07 13:32:04');

// this will return false because of invalid date 
$isDateISO = \Lib\Valid::dateTimeIso(12554747847847);

// this will return false because the given date is not ISO standard
$isDateISO = \Lib\Valid::dateTimeIso('08/27/2019');

```

###### Digits

Check if the given value is digits: 0-9

`static bool digits(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given value is digits 0-9 
- `bool` False if the given value is not digits

```php
// this will return true 
$isDigits = \Lib\Valid::digits(1230);

// this will return false because the value contains more characters than digits only 
$isDigits = \Lib\Valid::digits(47.25);
$isDigits = \Lib\Valid::digits('47.25');

// this will return false because the value is not digits
$isDigits = \Lib\Valid::digits('abc');
```

###### Email

Check if the given value is valid email address

`static bool email(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given value is valid email address 
- `bool` False if the given value is not valid email address

```php
// this will return true 
$isValidEmailAddress = \Lib\Valid::email('framework@duktig.solutions');

// this will return false because the value is not valid email address 
$isValidEmailAddress = \Lib\Valid::email(123);
$isValidEmailAddress = \Lib\Valid::email('47.25');
$isValidEmailAddress = \Lib\Valid::email('abc@');
$isValidEmailAddress = \Lib\Valid::email('abc@1_');
```

###### Float range

Check if the given value is float and in specified range

`static bool floatRange(mixed $value, float $min = NULL, float $max = NULL)`

Arguments:

- `mixed` Data to check
- `float` Minimal value (optional)
- `float` Maximal value (optional)

Return value:

- `bool` True if the given value is valid float number
- `bool` True if the given value is in specified range
- `bool` False if the given value is not a float number or out of specified range

```php
// this will return true 
$isFloat = \Lib\Valid::floatRange(12.34);
$isFloat = \Lib\Valid::floatRange(12.34, 10.00, 15.00);

// this will return false because the value is not a float number
$isFloat = \Lib\Valid::floatRange('abc');

// This will return false because the given value is out of specified range
$isFloat = \Lib\Valid::floatRange(12.56, 15.50, 20.50);
```

###### HTTP host

Check if the given value is valid HTTP host

`static bool httpHost(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given value is valid http host
- `bool` False if the given value is not a valid http host

```php
// this will return true
$isValidHost = \Lib\Valid::httpHost('www.example.com');

// this will return false
$isValidHost = \Lib\Valid::httpHost(123);
```

###### ID

Check if the given value is valid ID (Unique identifier)

`static bool id(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given value is valid ID `Integer > 0`
- `bool` False if the given value is not a valid Integer ID.

```php
// this will return true
$isValidId = \Lib\Valid::id(1);
$isValidId = \Lib\Valid::id(9856);
$isValidId = \Lib\Valid::id('89856');

// this will return false
$isValidId = \Lib\Valid::id('abc');
$isValidId = \Lib\Valid::id(0);
$isValidId = \Lib\Valid::id(-98);
```

> This method allows to check values for user Ids like Autoincrement in MySQL Database table.  

###### Int range

Check if the given value is integer and in specified range

`static bool intRange(mixed $value, int $min = NULL, int $max = NULL)`

Arguments:

- `mixed` Data to check
- `float` Minimal value (optional)
- `float` Maximal value (optional)

Return value:

- `bool` True if the given value is valid integer number
- `bool` True if the given value is in specified range
- `bool` False if the given value is not an integer number or out of specified range

```php
// this will return true 
$isInt = \Lib\Valid::intRange(12);
$isInt = \Lib\Valid::intRange(12, 10, 15);

// this will return false because the value is not an integer number
$isInt = \Lib\Valid::intRange('abc');

// This will return false because the given value is out of specified range
$isInt = \Lib\Valid::intRange(12, 15, 20);
```

###### IP Address

Check if the given value is valid IP Address

`static bool ipAddress(mixed $data)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given value is valid IP Address
- `bool` False if the given value is not a valid IP Address

```php
// this will return true 
$isIpAddress = \Lib\Valid::ipAddress('192.168.0.10');
$isIpAddress = \Lib\Valid::ipAddress('37.252.95.33');

// This will return false 
$isIpAddress = \Lib\Valid::ipAddress('12.25.26.36');
$isIpAddress = \Lib\Valid::ipAddress('abc');
$isIpAddress = \Lib\Valid::ipAddress(123); 
```

###### Json string

Check if the given value is valid Json string

`static mixed jsonString(mixed $string)`

Arguments:

- `mixed` Data to check

Return value:

- `bool` True if the given value is valid Json string
- `string` In case if json content is invalid the Json error message will be returned

```php
$string = '{
    "a":1,
    "b":"abc",
    "c":[1,2,3]
    }';
    
// This will return true
$jsonCheckResult = \Lib\Valid::jsonString($string);

// This will return Json Error message
$string = '{abc,345}';
$jsonCheckResult = \Lib\Valid::jsonString($string);

$string = 123;
$jsonCheckResult = \Lib\Valid::jsonString($string);
```

###### Password strength

Check the given string for password strength

`static int passwordStrength(mixed $data, int $min = 6, int $max = 128)`

Arguments:

- `mixed` Data to check
- `int` Minimum length = 6 (optional)
- `int` Maximum length = 128 (optional)

Return value:

- `int` **-1** Incorrect value
- `int` **0** not match
- `int` **1** weak
- `int` **2** not weak
- `int` **3** acceptable
- `int` **4** strong

```php
// this will return -1
$passwordStrength = \Lib\Valid::passwordStrength(['a' => 1]);

// this will return 0
$passwordStrength = \Lib\Valid::passwordStrength(123);
$passwordStrength = \Lib\Valid::passwordStrength('abc');

// this will return 0 because the length is out of range
$passwordStrength = \Lib\Valid::passwordStrength('abc#46&1!68fSh3&9~dfk%67@', 5, 20);

// Other cases will return password strength from 1 to 4
$passwordStrength = \Lib\Valid::passwordStrength('abc#46&1!68fSh3&9~dfk%67@');
$passwordStrength = \Lib\Valid::passwordStrength('abc#46&1!68fSh3&9~dfk%67@', 10, 25);
```

###### String length

Check if the given value is scalar type and the length is in specified range

`static bool stringLength(mixed $data, int $min = NULL, int $max = NULL)`

Arguments:

- `mixed` Data to check
- `int` Minimum length (optional)
- `int` Maximum length (optional)

Return value:

- `bool` True if the given value is scalar type and length is in specified range
- `bool` False if the given value is not a scalar type or the length is out of specified range

```php
// this will return true
$isStringLengthValid = \Lib\Valid::stringLength('Hello World!');
$isStringLengthValid = \Lib\Valid::stringLength('Hello World!', 5, 50);

// this will return false because the value is not a scalar type
$isStringLengthValid = \Lib\Valid::stringLength([1,2,3]);

// this will return false because the value length is out of specified range
$isStringLengthValid = \Lib\Valid::stringLength('Hello!', 10, 20);
```

###### URL

Check if the given value is a valid URL

`static bool url(mixed $data, int $flag = NULL)`

Arguments:

- `mixed` Data to check
- `int` Flag to check (optional) PHP Constant
    - **FILTER_FLAG_PATH_REQUIRED** - URL must have a path after the domain name (like www.example.com/example1/)
    - **FILTER_FLAG_QUERY_REQUIRED** - URL must have a query string (like "example.php?name=David&age=39")

Return value:

- `bool` True if the given value is valid URL address with required flag
- `bool` False if the given value is not a valid URL

```php
// this will return true
$isValidURL = \Lib\Valid::url('www.example.com');
$isValidURL = \Lib\Valid::url('www.example.com/home', FILTER_FLAG_PATH_REQUIRED);
$isValidURL = \Lib\Valid::url('www.example.com/index.php?a=1&b=2', FILTER_FLAG_QUERY_REQUIRED);

// this will return false because the value is not a valid URL
$isValidURL = \Lib\Valid::url(123);

// this will return false because the value is not matches with required flags 
$isValidURL = \Lib\Valid::url('www.example.com', FILTER_FLAG_PATH_REQUIRED);
$isValidURL = \Lib\Valid::url('www.example.com', FILTER_FLAG_QUERY_REQUIRED);
```

###### Latitude

Check if valid Latitude value: -90 and 90

Arguments:

- `mixed` Latitude

Return value:

- `bool` True if the given value is valid Latitude
- `bool` False if the given value is not a valid Latitude

```php
$isValidLatitude = \Lib\Valid::latitude('-40');
$isValidLatitude = \Lib\Valid::latitude(-40);
```

###### Longitude

Check if valid Longitude value: -180 and 90

Arguments:

- `mixed` Longitude

Return value:

- `bool` True if the given value is valid Longitude
- `bool` False if the given value is not a valid Longitude
  
```php
$isValidLatitude = \Lib\Valid::longitude('-40');
$isValidLatitude = \Lib\Valid::longitude(-40);
```

> See also [Validator library](validator.md). 

End of document
