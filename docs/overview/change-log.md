# Change log

Each release of **Duktig.Microservice** contains changes with logs for all functionality.  

#### New Validation rule option for Validation library

`ids` - New rule option in `array` rule, requires array to contain only id numbers. 

```php
<?php
// Validate if array contains only Ids
$validation = Validator::validateJson(
  $request->rawInput(),
  [
    # Array containing ids with specified length, min, max
    'ids_array1' => 'array:::ids',
    'ids_array2' => 'array:1:5:ids',
    'ids_array2' => 'array:1:5:ids:!required'
  ]
);
```

`unique` - New rule option in `array` rule, requires array to contain only unique values. 

```php
<?php
// Validate if array contains only Unique values
$validation = Validator::validateJson(
  $request->rawInput(),
  [
    # Array containing ids, unique values with specified length, min, max
    'unique_array1' => 'array:::unique',
    'ids_unique_array2' => 'array:1:5:ids:unique',
    'ids_unique_array2' => 'array:1:5:ids:unique:!required'
  ]
);
```


#### New Validation sub rule for Validation library

`!empty` - Sub-rule to disallow empty values

Value should be specified or not set in request/data structure array. 

```php
<?php
// Validate if array contains only Ids
$validation = Validator::validateJson(
  $request->rawInput(),
  [
    # URL not required
    'url2' => 'url:!required:!empty',
  ]
);
```

File: `kernel/lib/Validator.php`

#### New Database library to support PostgreSQL Server functionality

This library will provide the functionality to perform operations with PostgreSQL Database.

See [PostgreSQL](kernel/libraries/db/postgresql.md)

#### New Validation methods in `Valid` lib to validate for Latitude/Longitude and Date Time ISO: 8601

Version 1.2.0

```php
<?php

if(Valid::latitude($value)) {
  // code here
}

if(Valid::longitude($value)) {
  // code here
}

if(Valid::dateTimeIso($value)) {
  // code here
}

```

File: kernel/lib/Valid.php

#### New Validation rules in `Validator` lib to validate for Latitude/Longitude and Date Time ISO: 8601

```php
<?php

$validation = Validator::validateJson(
  $request->rawInput(),
  [
    'lat' => 'latitude',
    'lng' => 'longitude',
    'date_time' => 'date_time_iso'
  ]
);
```

File: `kernel/lib/Validator.php`

#### New System library `Env` allows to load environment variable files and get values. 

>NOTE: The system will load all environment variables from file `.env` located in project root dir by default 

```php
<?php

// This will be loaded by system in bootstrap. Default .env file located in project dir
\System\Env::load();

// Load custom environment file
$myCustomEnv = \System\Env::load('.my_env');

// Get item value from loaded environment variables
// the second argument of the method is default to return
$customValue = \System\Env::get('PROJECT_STATUS', 'development');
```

#### New way to load environment variables to Configuration files

The system will automatically load `.env` file as environment variables into project.

In case if a variable with the same name exists in the System/Docker container, the last will be used.

>NOTE: A Docker container environment variables always will replace values defined in environment file if exist. 

From now, Application configuration values setting from environment variables. 

End of document
