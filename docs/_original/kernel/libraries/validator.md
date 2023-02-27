# Duktig.Microservice
## Development Documentation

### Libraries

#### Validator

Version 1.1.0

Data structures validation class library.

> You can use this library to validate data structures with multiple rules.  
If you just want to validate a data with specific method, refer to - [Valid library](valid.md).

File: `kernel/lib/Validator.php`
Class: `Validator`

##### Methods

- [Duktig.Microservice](#duktigmicroservice)
  - [Development Documentation](#development-documentation)
    - [Libraries](#libraries)
      - [Validator](#validator)
        - [Methods](#methods)
          - [Validate at least one value](#validate-at-least-one-value)
          - [Validate data structure](#validate-data-structure)
          - [Validate exact keys values](#validate-exact-keys-values)
          - [Validate Json](#validate-json)
          - [Validate no extra values](#validate-no-extra-values)
          - [Validate rule](#validate-rule)

###### Validate at least one value

Validate if at least one value is not empty 

`static string validateAtLeastOneValue(mixed $values, array $itemsRequired)`

Arguments:

- `mixed` Data to validate
- `array` Required items (keys)

Return value:

- `string` Empty string in case if validation passed  
- `string` Error message. i.e. ***Required at least one value like: name, email, age***

```php
// At least one value required from the specified array. 
$requiredValues = ['name', 'email', 'age'];

// this will return empty string (pass the validation) because one of required value exists.
$dataToValidate = [
    'email' => 'framework@duktig.solutions'
];

$validationResult = \Lib\Validator::validateAtLeastOneValue($dataToValidate, $requiredValues);

// this will return error message, because no one exists
$dataToValidate = [
    'project' => 'Duktig.Microservice'
];

$validationResult = \Lib\Validator::validateAtLeastOneValue($dataToValidate, $requiredValues);
// message will look like: "Required at least one value like: name, email, age"
```

###### Validate data structure

Validate Array data structure with specified rules

`static array validateDataStructure(array $dataToValidate, array $validationRules, array $extraRules = [])`

Arguments:

- `array` Data to validate (this can be a multidimensional array)
- `array` Validation rules
- `array` Extra rules (optional)
 
Return value:

- `array` In case if validation passed, en empty array will be returned
- `array` If validation not passed, array with error messages will be returned

> For validation rules definition see method - [validateRule()](#validate-rule)

```php
// validation rules
$validationRules = [
    'name' => 'string_length:5:10', // String with length 5 - 10
    'email' => 'email',
    'age' => 'int_range:16:110', // Integer in range: 16 - 110
    'documents' => [
        'passport' => 'one_of:Yes:No', // string contains only: 'yes' or 'no'
        'driverLicense' => 'one_of:Yes:No'
    ]
];

// data structure to validate
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'documents' => [
        'passport' => 'yes',
        'driverLicense' => 'no'
    ]
];

// this will return empty array because the code listed above matches all validation rules.
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules); 

// invalid data structure to validate
$dataToValidate = [
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'documents' => [
        'passport' => 'abc',
        'driverLicense' => 'no'
    ]
];

// this will return array with error messages like:
/*
[
    'name' => 'Required string min 16 max 110',
    'documents' => [
        'passport' => 'Required value equal to yes | no'
    ]
]
*/
```

It is also possible to set rule as not required value.

For instance, the code listed bellow requires an email address, which accepts a valid email address or empty string.

```php
// validation rules
$validationRules = [
    'name' => 'string_length:5:10',
    'email' => 'email:!required'
];
 
// valid data because the email address is not required
$dataToValidate = [
    'name' => 'David'
];
 
// this will return en empty array
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules);
```

It is also possible to combine validation rules:

```php
// validation rules
$validationRules = [
    'amount' => 'digits|int_range:0:9',
    'email' => 'email|string_length:5:100'
];
 
// valid data because all values are valid with required rules
$dataToValidate = [
    'amount' => 5,
    'email' => 'framework@duktig.solutions'
];
 
// this will return en empty array
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules);

// in case if not valid data, the array with error messages will look like this:

// validation rules
$validationRules = [
    'amount' => 'digits|int_range:0:9',
    'email' => 'email|string_length:5:10'
];

// Data not matches with required rules
$dataToValidate = [
    'amount' => 'a',
    'email' => 'abc'
];
		
// this will return en empty array
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules);

// this will return an array with error messages
/*
Array
(
    [amount] => Array
        (
            [0] => Required digits
            [1] => Required int value min 0 max 9
        )

    [email] => Array
        (
            [0] => Required valid email address
            [1] => Required string min 5 max 10
        )

)
*/

```

This method also supports extra rules which are related to general data structure, instead of one specific item/rule. 

Extra rules listed bellow:  

- Validate if there is at least one value exists from rules - **at_least_one_value**
- Validate if data structure contains exact same keys as specified in validation rules - **exact_keys_values**
- Validate if there are any extra item in data structure - **no_extra_values**

Validating data to check if at least one value exists.

As you can see, each validation rule contains **!required** flag which means the data is not required. 
But the Extra rule **at_least_one_value** means that at least one value required. 

```php
// validation rules
$validationRules = [
    'name' => 'string_length:5:10:!required',
    'email' => 'email:!required'
];

$dataToValidate = [
    'name' => 'David'
];

$extraRules = [
    'general1' => 'at_least_one_value' // requires name or email address
];

// this will return an empty array (pass the validation), because at least one value exists: name
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// In case if there are no matching values, the array with error message will be returned.
$dataToValidate = [
    'message' => 'Hello World!'
];

$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// this will return array with error message like:
/*
Array
(
    [general1] => Array
        (
            [0] => Required at least one value like: name, email
        )

)
*/

// Another case, when we want to check at least one value from specified fields, not all.

// validation rules
$validationRules = [
    'name' => 'string_length:5:10:!required',
    'email' => 'email:!required',
    'phone' => 'string_length:5:15:!required'
];

$dataToValidate = [
    'name' => 'David',
    'phone' => '+37495565003'
];

$extraRules = [
    'general1' => 'at_least_one_value:email:phone' // requires phone or email
];

// this will return an empty array (pass the validation), because at least one value from required values list exists: phone
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// In case if there are no matching values, the array with error message will be returned.
$dataToValidate = [
    'name' => 'David'
];

$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// this will return array with error message like:
/*
Array
(
    [general1] => Array
        (
            [0] => Required at least one value like: email, phone
        )

)
```

Validating data if all keys exactly the same as required by rules. 

i.e. Rules defined for "name", "email", "age" and data structure contains data with exactly the same keys.

```php
// validation rules
$validationRules = [
    'name' => 'string_length:5:10',
    'email' => 'email',
    'age' => 'int_range:16:110'
];

$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40
];

$extraRules = [
    'general1' => 'exact_keys_values' // requires data structure with the same keys
];

// this will return an empty array (pass the validation), because all the keys are exacly the same as in required rules.
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// In case if there are no matching keys, the array with error message will be returned.
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
];

$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// this will return array with error message like:
/*
Array
(
    [age] => Array
        (
            [0] => Required int value min 16 max 110
        )

    [general1] => Array
        (
            [0] => Required exact values as: name, email, age
        )

)
*/

// In case if there are any extra key/value the validation will not passed.
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'message' => 'Hello!'
];

$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// this will return array with error message because there is extra value "message" in data structure.
/*
Array
(
    [general1] => Array
        (
            [0] => Required exact values as: name, email, age
        )

)
*/
```  

Validate if there are any extra item in data structure
 
```php
// validation rules
$validationRules = [
    'name' => 'string_length:5:10',
    'email' => 'email',
    'age' => 'int_range:16:110'
];

$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40
];

$extraRules = [
    'general1' => 'no_extra_values' // requires data structure with no extra values
];

// this will return an empty array (pass the validation), because all the keys are exacly the same as in required rules.
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// In case if there are any extra key/value in data structure, the array with error message will be returned
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'message' => 'Hello!'
];

$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

// this will return array with error message like:
/*
Array
(
    [general1] => Array
        (
            [0] => Required no extra values: message. Allowed only: name, email, age
        )

)
``` 

It is also possible to combine validation rules like:

```php
// validation rules
$validationRules = [
    'name' => 'string_length:5:10',
    'email' => 'email',
    'age' => 'int_range:16:110'
];

$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'message' => 'Hello!'
];

// as you see, we combined more than one validation rules with vertical bar "|".
$extraRules = [
    'general1' => 'no_extra_values|exact_keys_values'
];

// this will return the array with more than one error messages
$validationResult = \Lib\Validator::validateDataStructure($dataToValidate, $validationRules, $extraRules);

/*
Array
(
    [general1] => Array
        (
            [0] => Required no extra values: message. Allowed only: name, email, age
            [1] => Required exact values as: name, email, age
        )

)
*/

```

###### Validate exact keys values

Validate if the given data to validate has exactly the same keys as specified in rules

`static string validateExactKeysValues(mixed $values, array $itemsRequired)`

Arguments:

- `mixed` Data to validate (this can be a multidimensional array)
- `array` Required items to validate

Return value:

- `string` In case if validation passed, an empty string will be returned
- `string` If validation not passed, error message will be returned

```php
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40
];

// Note: In this example, the main goal is to test exact keys in Data struccture and Validation rules.  
$itemsRequired = [
    'name' => 'required',
    'email' => 'required',
    'age' => 'required'
];

// this will return an empty string (pass the validation) because the data structure keys are exactly the same as keys in rules.
$validationResult = \Lib\Validator::validateExactKeysValues($dataToValidate, $itemsRequired);

// In case if there are any missing or extra value, the error message will be returned like:

// Missing value
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
];

// Extra value
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'message' => 'Hello!'
];

/*
Required exact values as: name, email, age
*/
```

###### Validate Json

Validate a json string with specified rules

`static array validateJson(mixed $jsonStringToValidate, array $validationRules = [], array $extraRules = [])`

Arguments:

- `mixed` Data to validate (should be Json string)
- `array` Validation rules (this can be a multidimensional array of rules depends on data structure)
- `array` Extra validation rules (optional)

Return value:

- `array` Return empty array if validation passed
- `array` Return array with validation error messages

> This method is exactly the same as **validateDataStructure()**. Just instead of array data structure we set Json string to validate.   

```php
// Validation data: Json string instead of Array Data structure
$jsonStringToValidate = '{"name":"David","email":"framework@duktig.solutions","age":40}';

// validation rules
$validationRules = [
    'name' => 'string_length:5:10',
    'email' => 'email',
    'age' => 'int_range:16:110'
];

// This will return an empty array because all values in json is valid.
$validationResult = \Lib\Validator::validateJson($jsonStringToValidate, $validationRules);

// Let's check with invalid data to see the returned array with error messages

// Validation data: Json string with missed data
$jsonStringToValidate = '{"name":"David","message":"Hello World!"}';

$validationResult = \Lib\Validator::validateJson($jsonStringToValidate, $validationRules);

// this will return the array with error message like:

/*
Array
(
    [email] => Array
        (
            [0] => Required valid email address
        )

    [age] => Array
        (
            [0] => Required int value min 16 max 110
        )

)
*/
```

This method accepts multidimensional data with rules as listed in code bellow:

```php
// Validation data: Json string instead of Array Data structure
$jsonStringToValidate = '{
    "name": "David",
    "email": "framework@duktig.solutions",
    "age": 40,
    "documents": {
         "passport": "Yes",
         "driverLicense": "No"
    }
}';

// validation rules
$validationRules = [
    'name' => 'string_length:5:10',
    'email' => 'email',
    'age' => 'int_range:16:110',
    'documents' => [
        'passport' => 'one_of:Yes:No', // string contains only: 'yes' or 'no'
        'driverLicense' => 'one_of:Yes:No'
    ]
];

// This will return an empty array because all values in json is valid.
$validationResult = \Lib\Validator::validateJson($jsonStringToValidate, $validationRules);

// Let's test with invalid data to see, how the error messages returned in multidimensional data structure.
// Json data not matches with required rules  
$jsonStringToValidate = '{
    "name": "David",
    "email": "123",
    "age": 39,
    "documents": {
         "passport": "Yes",
         "driverLicense": "abc"
    }
}';

// this will return the array with error messages 
$validationResult = \Lib\Validator::validateJson($jsonStringToValidate, $validationRules);

/*
Array
(
    [email] => Array
        (
            [0] => Required valid email address
        )

    [documents] => Array
        (
            [driverLicense] => Array
                (
                    [0] => Required value equal to Yes | No
                )

        )

)
*/
``` 

In case if there are any syntax error in Json string, the array with error message will be returned:

```php
/*
Array
(
    [0] => Json Error 4: Syntax error. Required valid json string
)
*/
```

> The second: Validation rules argument is optional. If it is null, the method will just validate the Json string for syntax.
> Extra rules argument explained in [validateDataStructure()](#validate-data-structure)

###### Validate no extra values

Validate if there are any extra value in given data structure

`static string validateNoExtraValues(mixed $values, array $itemsRequired)`

Arguments:

- `mixed` Data to validate
- `array` Required value keys to compare

Return value:

- `string` Return empty string if validation passed
- `string` Return validation error message

```php
// Data to validate
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40
];

// Note: In this example, the main goal is to validate if there are any extra values  
$itemsRequired = [
    'name' => 'required',
    'email' => 'required',
    'age' => 'required'
];

// this will return an empty string (pass the validation) because the data structure keys are exactly the same as keys in rules.
$validationResult = \Lib\Validator::validateNoExtraValues($dataToValidate, $itemsRequired);

// In case if there are any extra value, the error message will be returned like:

// Extra value
$dataToValidate = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40,
    'message' => 'Hello!'
];

/*
Required no extra values: message. Allowed only: name, email, age
*/
```

###### Validate rule

Validate given value with specified rule(s)

`static string validateRule(mixed $value, string $rule, ?bool $isset = true)`

Arguments:

- `mixed` Data to validate
- `string` Validation rule (explained bellow)
- `bool` The value to validate is set. Assuming in data structure it defined even if empty.
    
**Validation rule** is the string pattern - rule name and options divided by colon `:`.      

- The pattern of rule looks like: `{rule-name}:{rule-option-1}:{rule-option-N}:{not-required}`. 
   
   For instance, let's define a rule which requires a password string with minimum length 10, 
   maximum length 100 and password strength 3 (acceptable) - `password:10:100:3`
    
- In some rules definition it is possible to skip option value like: `password:10::3`. 
  As you can see, the second option is skipped and the validation process will set it by default.
  
  Another example where we skipping first option and setting only second option: `int_range::100`    
       
- The option `!required` means that the value can be empty or valid with requirements.

  For instance, this rule requires valid email address or empty string: `email:!required`.   

- The option `!empty` assuming if even the value is not required, it cannot be empty.
    
  ```php
  // Validation rule: 'ids_array:!required:!empty' 
  
  // 'a' is empty. Even if !required sub-rule specified, the array cannot be empty. 
  $validationData = [
    'k' => 'test',
    'a' => []
  ];

  // In this case, validation will passed because 'a' is not set and not required.
  $validationData = [
    'k' => 'test'
  ];
  ```
  
See all rules listed bellow
    
- `required`
    - Requirement: None empty data
    - Pattern: ***required***
    
- `credit_card`
    - Requirement: Valid credit card number
    - Pattern: ***credit_card:{not-required}***
    - Pattern examples:
        - `credit_card` Required a valid credit card number
        - `credit_card:!required` Required a valid credit card number or empty string
            
- `password`
    - Requirement: Not a weak password
    - Pattern: ***password:{min-length = 6}:{max-length = 128}:{password-strength = 2}:{not-required}***
    - Password strength: ***1=weak, 2=not weak, 3=acceptable, 4=strong***
    - Pattern examples:
        - `password` equivalent to `password:6:128:2` 
        - `password:10` equivalent to `password:10:128:2`
        - `password:10:100` equivalent to `password:10:100:2`
        - `password:10:100:3` 
        - `password::::!required` equivalent to `password:6:128:3:!required`
        - `password:10:100:3:!required`

- `email`
    - Requirement: Valid email address
    - Pattern: ***email:{not-required}***
    - Pattern examples:
        - `email` Required a valid email address
        - `email:!required` Required a valid email address or empty string

- `id`
    - Requirement: Valid ID (Integer Number > 0)
    - Pattern: ***id:{not-required}***
    - Pattern examples:
        - `id` Required a valid ID (Integer Number > 0)
        - `id:!required` Required a valid ID (Integer Number > 0) or empty string
            
- `digits` 
    - Requirement: Digits 0-9
    - Pattern: ***digits:{not-required}***
    - Pattern examples:
        - `digits` Required a digits 0-9
        - `digits:!required` Required a digits 0-9 or empty string
    
- `digits_separated`
    - Requirements: Digits separated by specified character
    - Pattern: ***digits_separated:{min-digits}:{max-digits}:{separator}:{not-required}***
    - Pattern examples:
        - `digits_separated:1:5:,` Required 1 - 5 digits separated by "," (comma) 
        - `digits::10:-:!required` 0 - 10 digits separated by "-", Not required value     
    
- `int_range`
    - Requirement: Integer number in specified range
    - Pattern: ***int_range:{min-value = null}:{max-value = null}:{not-required}***             
    - Pattern examples:
        - `int_range` validate integer number 
        - `int_range:10` validate integer number **min = 10**
        - `int_range:10:100` validate integer number **min = 10**, **max = 100**
        - `int_range:10:100:!required` validate integer number **min = 10**, **max = 100**, Not required value  
        - `int_range:::!required` validate integer number - not required value
        - `int_range::100` validate integer number **max = 100**

- `float_range`
    - Requirement: Float number in specified range
    - Pattern: ***float_range:{min-value = null}:{max-value = null}:{not-required}***
    - Pattern examples:
        - `float_range` validate float number 
        - `float_range:10.55` validate float number **min = 10.55**
        - `float_range:10.55:100.83` validate float number **min = 10.55**, **max = 100.83**
        - `float_range:10.55:100.83:!required` validate float number **min = 10.55**, **max = 100.83**, Not required value  
        - `float_range:::!required` validate float number - not required value
        - `float_range::100.83` validate float number **max = 100.83**

- `ip_address`
    - Requirement: Valid IP address
    - Pattern: ***ip_address:{not-required}***
    - Pattern examples:
        - `ip_address` Required a valid IP address
        - `ip_address:!required` Required a valid IP address or empty string
                
- `http_host`
    - Requirement: Valid http host
    - Pattern: ***http_host:{not-required}***
    - Pattern examples:
        - `http_host` Required valid HTTP host
        - `http_host:!required` Required valid HTTP host or empty string        
        
- `alphanumeric`
    - Requirement: Alphanumeric value a-z 0-9
    - Pattern: ***alphanumeric:{not-required}*** 
    - Pattern examples:
        - `alphanumeric` Required alphanumeric string
        - `alphanumeric:!required` Required alphanumeric or empty string
            
- `alpha`
    - Requirement: Alphabetical value a-z
    - Pattern: ***alpha:{not-required}***
    - Pattern examples:
        - `alpha` Required alphabetical string
        - `alpha:!required` Required alphabetical or empty string 
        
- `string_length`
    - Requirement: String with required length
    - Pattern: ***string_length:{min-length = null}:{max-length = null}:{not-required}***
    - Pattern examples:
        - `string_length` validate string if scalar value 
        - `string_length:10` validate string **min length = 10**
        - `string_length:10:100` validate string **min length = 10**, **max length = 100**
        - `string_length:10:100:!required` validate string **min length = 10**, **max length = 100**, Not required value
        
- `url`
    - Requirement: Valid URL address with specified options
    - Pattern: ***url:{filter-flag}:{not-required}***
    - Pattern examples (Patterns listed below are PHP Constants): 
        - `url:FILTER_FLAG_PATH_REQUIRED` URL must have a path after the domain name (like www.example.com/example1/) 
        - `url:FILTER_FLAG_QUERY_REQUIRED` URL must have a query string (like "example.php?name=David&age=39")
        - `url:FILTER_FLAG_QUERY_REQUIRED:!required` same as above but not required
        
        
- `date_iso`
    - Requirement: Valid with ISO standard 8601: Y-m-d
    - Pattern: ***date_iso:{not-required}***
    - Pattern examples: 
        - `date_iso` Required valid date with ISO standard
        - `date_iso:!required` Required valid date with ISO standard **Y-m-d** or empty string (not required)  
    
- `date_time_iso`
    - Requirement: Valid date-time with ISO standard 8601: Y-m-d H:i:s
    - Pattern: ***date_time_iso:{not-required}***
    - Pattern examples:
        - `date_time_iso` Required valid date-time with ISO standard
        - `date_time_iso:!required` Required valid date-time with ISO standard **Y-m-d H:i:s** or empty string (not required)
    
- `date`
    - Requirement: Valid date with given format (default Y-m-d)
    - Pattern: ***date:{date-format}:{not-required}***
    - Pattern examples:
        - `date` Date must be defined with default format: `Y-m-d`
        - `date:m/d/Y` Date must be defined with given format: `m/d/Y` 
        - `date:!required` Date must be defined with default `Y-m-d` format or empty string (not required)
        - `date:m/d/Y:!required` Date must be defined with given format: `m/d/Y` or empty string (not required) 
            
- `date_equal_or_after`
    - Requirement: Date equal or after specified date
    - Pattern: ***date_equal_or_after:{date-to-check-for-equal-or-after}:{date-format}:{not-required}***
    - Pattern examples:
        - `date_equal_or_after:2019-08-31:Y-m-d` Date should be equal or after **2019-08-31** with specified format. 
        - `date_equal_or_after:08/31/2019:m/d/Y` Date should be equal or after **08/31/2019** with specified format. 
        - `date_equal_or_after:2019-08-31:Y-m-d:!required` Date should be equal or after **2019-08-31** with specified format or can by empty string (not required).

- `date_equal_or_before`
    - Requirement: Date equal or before specified date
    - Pattern: ***date_equal_or_before:{date-to-check-for-equal-or-before}:{date-format}:{not-required}***
    - Pattern examples:
        - `date_equal_or_before:2019-08-31:Y-m-d` Date should be equal or before **2019-08-31** with specified format. 
        - `date_equal_or_before:08/31/2019:m/d/Y` Date should be equal or before **08/31/2019** with specified format. 
        - `date_equal_or_before:2019-08-31:Y-m-d:!required` Date should be equal or before **2019-08-31** with specified format or can by empty string (not required).
     
- `date_between`
    - Requirement: Date between two dates
    - Pattern: ***date_between:{start-date}:{end-date}:{date-format}:{not-required}***
    - Pattern examples:
        - `date_between:2019-08-01:2019-08-31:Y-m-d` Date should be between two dates with specified format.
        - `date_between:08/01/2019:08/31/2019:m/d/Y` Date should be between two dates with specified format.
        - `date_between:08/01/2019:08/31/2019:m/d/Y:!required` Date should be between two dates with specified format or empty string (not required).

- `json_string`
    - Requirement: Valid Json string
    - Pattern: ***json_string:{not-required}***
    - Pattern examples:
        - `json_string` Requires valid Json string
        - `json_string:!required` Requires valid Json or empty string (not required)
    
- `equal_to`
    - Requirement: Equal to specified value
    - Pattern: ***equal_to:{equal-to-val}:{not-required}***
    - Pattern examples:
        - `equal_to:abc` Data should be equal to **abc**
        - `equal_to:abc:!required` Data should be equal to **abc** or empty string (not required)

- `not_equal_to`
    - Requirement: Not equal to specified value
    - Pattern: ***not_equal_to:{not-equal-to-val}:{not-required}***
    - Pattern examples:
        - `not_equal_to:abc` Data should not be equal to **abc**
        - `not_equal_to:abc:!required` Data should not be equal to **abc** or can be an empty string (not required)

- `one_of`
    - Requirement: Equal to one of values
    - Pattern: ***one_of:{equal-to-val-1}:{equal-to-val-N}:{not-required}***
    - Pattern examples:
        - `one_of:a:b:c` Data should be equal to one of given values **a**, **b**, **c**
        - `one_of:a:b:c:!required` Data should be equal to one of given values **a**, **b**, **c** or empty string (not required)

- `not_one_of`
    - Requirement: Not equal to any of values
    - Pattern: ***not_one_of:{not-equal-to-val-1}:{not-equal-to-val-N}:{not-required}***
    - Pattern examples:
        - `not_one_of:a:b:c` Data should not be equal to any of given values **a**, **b**, **c**
        - `not_one_of:a:b:c:!required` Data should not be equal to any of given values **a**, **b**, **c** or can be an empty string (not required)

- `array`
    - Requirement: Array
    - Pattern: ***array:{min-elements}:{max-elements}:{unique-data}:{id-numbers}:{not-required}*** 
    - Pattern examples:
        - `array` Requires an array
        - `array:!required` Requires an array or empty (not required)
        - `array:0:10` Requires an array with elements count 0-10
        - `array:5:15:!required` Requires an array with elements count 5-15 or empty (not required)
        - `array:4` Requires an array with minimum 4 elements
        - `array::40` Requires an array with maximum 40 elements
        - `array:10::!required` Requires an array with minimum 10 elements or empty (not required)
        - `array:10:20:{unique}` Requires an array containing only unique values.
        - `array:10:20:{ids}` Requires an array containing only Id numbers.
        - `array:10:20:{unique}:{ids}` Requires an array containing only Unique Id numbers.

- `latitude`
    - Requirement: Valid Latitude value: -90 and 90 
    - Pattern: ***latitude:{not-required}*** 
    - Pattern examples:
        - `latitude` Requires a valid Latitude value
        - `latitude:!required` Requires a valid Latitude value or empty (not required)
         
- `longitude`
    - Requirement: Valid Longitude value: -180 and 90
    - Pattern: ***longitude:{not-required}*** 
    - Pattern examples:
        - `longitude` Requires a valid Longitude value
        - `longitude:!required` Requires a valid Longitude value or empty (not required)
     
Return value:

- `string` Return empty string if validation passed
- `string` Return validation error message

> See more detailed code in: app/controllers/Tests/Validation.php

```php
// Validate if the given value is valid email address
$validationResult = \Lib\Validator::validateRule('framework@duktig.solutions', 'email');

// Validate if the given value is string with length 5-6
$validationResult = \Lib\Validator::validateRule('Hello', 'string_length:5:10');

// Validate if the given value is string with length 5-6 or empty string (Not required)
$validationResult = \Lib\Validator::validateRule('Hello', 'string_length:5:10:!required');
```

> See also [Valid library](valid.md).

> For more details see `app/controllers/Test.php` file with a lot of examples.

End of document
