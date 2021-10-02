# Duktig.Microservice
## Development Documentation

### Change log

Each release of **Duktig.Microservice** contains changes with logs for all functionality.  

#### New Validation rule option for Validation library

`ids` - New rule option in `array` rule, requires array to contain only id numbers. 

```php
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

End of document



