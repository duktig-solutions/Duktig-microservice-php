# Duktig.Microservice
## Development Documentation

### Libraries

#### PostgreSQL

PostgreSQL Database class library

All Database models in Duktig.Microservice extends this base class to work with PostgreSQL Database.

For instance:

File: app/Models/Examples/PostgreSqlTestModel.php

```php
/**
 * Class UnitStructure
 *
 * @package App\Models\Examples
 */
class UnitStructure extends \Lib\Db\PostgreSQL {

    private $table = 'unit_structures';

    public function __construct() {
        $config = Config::get()['Databases']['DataWareHouse'];
        parent::__construct($config);
    }
    
    public function getUnitStructureById($id) {
        return $this->fetchAssocByWhere($this->table, ['unit_structure_id' => $id]);
    }

    // Other methods here
}    
```

> More detailed models development explained in "[Create a **Model** class](../../../app/model.md)".  

> NOTE: Because Database models extends this class library, all example codes listed bellow will use `$this->` which means the model instance.  

File: `kernel/lib/db/PostgreSQL.php`
Class: `PostgreSQL`

##### Methods

- [Duktig.Microservice](#duktigmicroservice)
  - [Development Documentation](#development-documentation)
    - [Libraries](#libraries)
      - [PostgreSQL](#postgresql)
        - [Methods](#methods)
          - [Insert data](#insert-data)
          - [Insert batch data](#insert-batch-data)
          - [Update data](#update-data)
          - [Delete data](#delete-data)
          - [Execute query](#execute-query)
          - [Execute query and return affected rows](#execute-query-and-return-affected-rows)
          - [Fetch all assoc](#fetch-all-assoc)
          - [Fetch Assoc](#fetch-assoc)
          - [Fetch All Assoc By Where](#fetch-all-assoc-by-where)
          - [Fetch All Fields Assoc By Where](#fetch-all-fields-assoc-by-where)
          - [Fetch Assoc By Where](#fetch-assoc-by-where)
          - [Fetch Fields Assoc By Where](#fetch-fields-assoc-by-where)
          - [Escape string](#escape-string)
        - [Transactions](#transactions)
          - [Begin transaction](#begin-transaction)
          - [Commit transaction](#commit-transaction)
          - [Rollback transaction](#rollback-transaction)
    

###### Insert data

Insert data into PostgreSQL Database table

`final int insert(string $table, array $data, ?string $returnInsertIdFieldName = null)`

Arguments:

- `string` Table to insert
- `array` Data to insert
- `returnInsertIdFieldName` return last insert id by this field value ( i.e. user_id).
 
Return value:

- `int` Last insert Id

```php
$table = 'users';

$record = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 40
];

// return last insert Id
$id = $this->insert($table, $record, 'user_id');
```

###### Insert batch data

Insert batch data into MySQL Database table

> Note: If the value $returnInsertIdFieldName is not null, the batch insert will return an array of inserted ids.

`final array insertBatch(string $table, array $fields, array $data, ?string $returnInsertIdFieldName = null)`

Arguments:

- `string` Table to insert
- `array` Table fields
- `array` Data to insert
- `returnInsertIdFieldName` return last insert id by this field value ( i.e. user_id).
 
Return value:

- `array` Array with last insert Ids

```php
$table = 'users';

$fields = [
    'name', 'email', 'age'
];

$records = [
    ['David', 'framework@duktig.solutions', 40],
    ['Hakob', 'hakob.ayvazyan@gmail.com', 15]
];

// this will return array with new inserted Ids
$insertIds = $this->insertBatch($table, $fields, $records, 'user_id');
```

###### Update data

Update data with where condition

`final int update(string $table, array $data, array $where)`

Arguments:

- `string` Table to update
- `array` Data to update
- `array` Where condition data

Return value:

- `int` affected rows count

```php
$table = 'users';

$record = [
    'name' => 'David',
    'email' => 'framework@duktig.solutions',
    'age' => 39
];

$where = [
    'id' => 13,
    'email' => 'framework@duktig.solutions'
];

// this will return affected rows count
$affectedRowsCount = $this->update($table, $record, $where);
```

###### Delete data

Delete table data by where condition

`final int delete(string $table, array $where)`

Arguments:

- `string` Table name
- `array` Where condition data

Return value:

- `int` affected rows count

```php
$table = 'users';

$where = [
    'id' => 13,
    'email' => 'framework@duktig.solutions'
];

// this will return affected rows count
$affectedRowsCount = $this->delete($table, $where);
```

###### Execute query

Execute PostgreSQL Query

`final mixed query(string $queryString, array $params = NULL)`

Arguments:

- `string` Table name
- `array` Query parameters

Return value:

- `PgSql\Result|false` PostgreSQL query result or false in case of fail

Let's execute query with **select** statement and get rows from table.

```php
$params = [
    'framework@duktig.solutions'
];

// Execute query to get one row
$result = $this->query('select * from users where email = $1 ', $params);

// Call fetch method to get row.
while ($row = pg_fetch_row($result)) {
  echo "Author: $row[0]  E-mail: $row[1]";
}

// Let's try to run the query and get many rows
// Execute query to get one row
$result = $this->query('select * from users where status = 1');

$rows = pg_fetch_all($result);

```

Now, let's try to execute a query to update/delete data in table

```php
$params = [
    2, 'test@example.com'
];

// Execute query to update records and get PostgreSQL Result
$result = $this->query("update users set status = $1 where email = $2 ", $params);

// Another example to delete records and get PostgreSQL Result
$result = $this->query("delete from users where status = $1 and email = $2 ", $params);
```

###### Execute query and return affected rows

Execute PostgreSQL Query

`final mixed queryWithAffectedRows(string $queryString, array $params = NULL)`

Arguments:

- `string` Table name
- `array` Query parameters

Return value:

- `int|false` Affected rows or false in case of fail

Let's execute query and get affected rows.

```php
$params = [
    2, 'test@example.com'
];

// Execute query to update records and get affected rows 
$affectedRows = $this->query("update users set status = $1 where email = $2 ", $params);

// Another example to delete records and get affected rows
$affectedRows = $this->query("delete from users where status = $1 and email = $2 ", $params);
```

###### Fetch all assoc

Fetch multiple rows as assoc array 

`final array fetchAllAssoc(string $queryString, array $params = NULL)`

Arguments:

- `string` Query string
- `array` Bind parameters (optional) 

Return value:

- `array` Assoc array of multiple rows

```php
// Fetch rows by specified criteria
$rows = $this->fetchAllAssoc(
    'select * from users where email = $1',
    ['test@example.com']
);

// Fetch all rows
$rows = $this->fetchAllAssoc(
    'select * from settings'
);
```

###### Fetch Assoc

Fetch one row as assoc array

`final array fetchAssoc(string $queryString, array $params = NULL)`

Arguments:

- `string` Query string
- `array` Bind parameters (optional) 

Return value:

- `array` Row Assoc array

```php
// Fetch row by specified criteria
$row = $this->fetchAssoc(
    'select * from users where email = $1',
    ['test@example.com']
);

// Fetch row without bind param
$row = $this->fetchAssoc(
    "select * from settings where `item` = 'lastUpdate' "
);
```

###### Fetch All Assoc By Where

Fetch all rows as assoc array by where condition. 

`final array fetchAllAssocByWhere(string $table, array $where)`

Arguments:

- `string` Table to select
- `array` Where condition 

Return value:

- `array` Rows Assoc array

The where condition parameters will be converted to query string with "and" operator.

```php
// define where condition
$where = [
    'status' => 2,
    'type' => 1
];

// fetch rows as assoc array by where condition
// This method will convert all parameters to query as: 
// select * from users where status = '2' and type = '1' 
$result = $this->fetchAllAssocByWhere('users', $where);
```

###### Fetch All Fields Assoc By Where

Fetch specified rows as assoc array by where condition. 

`final array fetchAllFieldsAssocByWhere(string $table, array, $fields, array $where)`

Arguments:

- `string` Table to select
- `array` Fields to select
- `array` Where condition 

Return value:

- `array` Rows Assoc array

The where condition parameters will be converted to query string with "and" operator.

```php
// defind fields to select
$fields = [
	'id',
	'firstName',
	'lastName'
];

// define where condition
$where = [
    'status' => 2,
    'type' => 1
];

// fetch specified rows as assoc array by where condition
// This method will convert all parameters to query as: 
// select id, firstName, lastName from users where status = '2' and type = '1' 
$result = $this->fetchAllAssocByWhere('users', $fields, $where);
```

###### Fetch Assoc By Where

Fetch one row as assoc array by where condition.

`final array fetchAssocByWhere(string $table, array $where)`

Arguments:

- `string` Table to select
- `array` Where condition 

Return value:

- `array` Row Assoc array

```php
// define where condition
$where = [
    'email' => 'framework@duktig.solutions'
];

// fetch row as assoc array by where condition
$result = $this->fetchAssocByWhere('users', $where);
```

The where condition parameters will be converted to query string with "and" operator.

```php
$where = [
   'id' => 55,
   'email' => 'framework@duktig.solutions',
   'status' => 2 
];

// the query of this method will convert to: select * from users where id = 55 and email = 'framework@duktig.solutions' and status = '2';
$result = $this->fetchAssocByWhere('users', $where);
```

###### Fetch Fields Assoc By Where

Fetch one row specified fields as assoc array by where condition.

`final array fetchFieldsAssocByWhere(string $table, array $fields, array $where)`

Arguments:

- `string` Table to select
- `array` Fields to select
- `array` Where condition 

Return value:

- `array` Row Assoc array

```php
// define where condition
$fields = [
	'id',
	'firstName',
	'lastName'
];

$where = [
    'email' => 'framework@duktig.solutions'
];

// fetch row as assoc array by where condition
$result = $this->fetchFieldsAssocByWhere('users', $fields, $where);
```

The where condition parameters will be converted to query string with "and" operator.

```php
$where = [
   'id' => 55,
   'email' => 'framework@duktig.solutions',
   'status' => 2 
];

// the query of this method will convert to: select id, firstName, lastName from users where id = 55 and email = 'framework@duktig.solutions' and status = '2';
$result = $this->fetchFieldsAssocByWhere('users', $fields, $where);
```

###### Escape string

Escape string for query 

`final mixed escape(string $value)`

Arguments:

- `mixed` Value to escape 

Return value:

- `string` Escaped string

```php
$string = "test string with 'single' quotes";

$query_string = "insert into articles (title) values ('" . $this->escape($string) . "') ";

$this->query($query);

```

> Notice: In this example we used a query method with escaped string just to demonstrate the functionality.
> With this library it is better to use:
> 
> ```php
> $this->query(
>     "insert into articles (title) values ($1) ", ["test string with 'single' quotes"]); 
> );
> ```    

##### Transactions

PostgreSQL Library transaction functionality

###### Begin transaction

`final void beginTrans(bool $autoCommit = false)`

###### Commit transaction

`final void commitTrans()`

###### Rollback transaction

`final void rollbackTrans()`

```php
//Begin transaction
$this->beginTrans();

try {
    
    // Execute queries
    $this->query("update unit_Structures set data_structures = $1 where title = $2", ['{"test":"abcs_name_'.time().'","num":999}', 'update_this']);
    $this->query("update unit_Structures set data_structures = $1 where title_invalid_field_name = $2", ['{"test":"abc_'.time().'"}', 'update_this']);
    
    // Commit transaction
    $this->commitTrans();
    return 'This should not work';
    
} catch(\Throwable $e) {

    // Rollback transaction in case if any of queries execution fail
    $this->rollbackTrans();
    return 'ok';
    
}
```

> See also [Create a **Model** class](../../../app/model.md)

End of document
