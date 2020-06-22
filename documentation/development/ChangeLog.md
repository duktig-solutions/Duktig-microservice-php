# Duktig.Microservice
## Development Documentation

### Change log

Each release of **Duktig.Microservice** contains changes with logs for all functionality.  

#### New Validation rule for Validation library

`ids_array` - Validate if given array contains only ids. 

```php
// Validate if array contains only Ids
$validation = Validator::validateJson(
  $request->rawInput(),
  [
    # Array containing ids with specified length, min, max
    'ids_array1' => 'ids_array',
    'ids_array2' => 'ids_array:1:5',
    'ids_array2' => 'ids_array:1:5:!required'
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



