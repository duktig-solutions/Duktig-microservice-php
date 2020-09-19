# Duktig.Microservice
## Development Documentation

### Libraries

#### MySQLi

MySQL Database class library

All Database models in Duktig.Microservice extends this class to work with MySQL Data.

For instance:

File: app/models/User.php

```php
/**
 * Class User
 *
 * @package App\Models
 */
class User extends \Lib\Db\MySQLi {

    public function __construct() {

        $config = \System\Config::get()['Databases']['DefaultConnection'];

        parent::__construct($config);
    }
    
    // Other methods here
}    
```

> More detailed models development explained in "[Create a **Model** class](../../../app/model.md)".  

> NOTE: Because Database models extends this class library, all example codes listed bellow will use `$this->` which means the model instance.  

File: `kernel/lib/db/MySQLi.php`
Class: `MySQLi`

##### Methods

- [insert() - Insert data](#insert-data)
- [insertBatch() - Insert batch data](#insert-batch-data)
- [update() - Update data](#update-data)
- [delete() - Delete data](#delete-data)
- [query() - Execute query](#execute-query)
- [asyncQueries() - Execute asynchronous queries](#execute-asynchronous-queries)
- [fetchAllAssoc() - Fetch all rows as assoc array](#fetch-all-assoc)
- [fetchAssoc() - Fetch row as assoc array](#fetch-assoc)
- [fetchAllAssocByWhere() - Fetch all rows as assoc array by where condition](#fetch-all-assoc-by-where)
- [fetchAssocByWhere() - Fetch row as assoc array by where condition](#fetch-assoc-by-where)
- [escape() - Escape string](#escape-string)
- [Transactions](#transactions)
    - [beginTrans() - Begin transaction](#begin-transaction)
    - [commitTrans() - Commit transaction](#commit-transaction)
    - [rollbackTrans() - Rollback transaction](#rollback-transaction)
    

###### Insert data

Insert data into MySQL Database table

`final int insert(string $table, array $data)`

Arguments:

- `string` Table to insert
- `array` Data to insert

Return value:

- `int` Last insert Id

```php
$table = 'users';

$record = [
    'name' => 'David',
    'email' => 'software@duktig.dev',
    'age' => 40
];

// return last insert Id
$id = $this->insert($table, $record);
```

###### Insert batch data

Insert batch data into MySQL Database table

> Note: This method uses a transaction. If one of query is triggers an error, Transaction Rollback will be run.  

`final array insertBatch(string $table, array $fields, array $data)`

Arguments:

- `string` Table to insert
- `array` Table fields
- `array` Data to insert

Return value:

- `array` Array with last insert Ids

```php
$table = 'users';

$fields = [
    'name', 'email', 'age'
];

$records = [
    ['David', 'software@duktig.dev', 40],
    ['Hakob', 'hakob.ayvazyan@gmail.com', 15]
];

// this will return array with new inserted Ids
$insertIds = $this->insertBatch($table, $fields, $records);
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
    'email' => 'software@duktig.dev',
    'age' => 39
];

$where = [
    'id' => 13,
    'email' => 'software@duktig.dev'
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
    'email' => 'software@duktig.dev'
];

// this will return affected rows count
$affectedRowsCount = $this->delete($table, $where);
```

###### Execute query

Execute MySQL Query

`final mixed query(string $queryString, array $params = NULL)`

Arguments:

- `string` Table name
- `array` Query parameters

Return value:

- `mysqli_result` MySQLi result object if data selected
- `int` Affected rows in case if delete, update ... 

Let's execute query with **select** statement and get rows from table.

```php
$params = [
    'software@duktig.dev'
];

// Execute query to get one row
$result = $this->query('select * from users where email = ? ', $params);

// Call fetch method to get row.
$row = $result->fetch_assoc();

$params = [
    '%@example.com'
];

// Execute query to get many records
$result = $this->query('select * from users where email like ? ', $params);

// Call fetch_all to get many records as assoc array 
$rows = $result->fetch_all(MYSQLI_ASSOC);
```

Now, let's try to execute a query to update/delete data in table

```php
$params = [
    2, '%@example.com'
];

// Execute query to update records and get affected rows 
$affectedRows = $this->query("update users set status = ? where email like ? ", $params);

// Another example to delete records and get affected rows
$affectedRows = $this->query("delete from users where status = ? and email like ? ", $params);
```

###### Execute asynchronous queries

Execute Asynchronous queries and return a results array.
Results will be sorted in array with queries order.
i.e. 5th element in result array is the result of 5th query.
     
`final array asyncQueries(array $queries)`

Arguments:

- `array` Queries array 
    
    Each query contains a query string and connection configuration (optional).
    If Connection configuration not set, query will execute with library first initialized connection.
    
    **This assumes that each query can execute in separate Database (connection).**
       
    Example of queries array where each query will execute in different databases.
    
    ```php
    $queries = [
        [
          'query' => "update users set status = 2 where email like '@example.com' ",
          'config' => [
              'driver' => 'MySQLi',
              'host' => 'localhost',
              'port' => 3306,
              'username' => '{user}',
              'password' => '{pwd}',
              'database' => '{db}',
              'charset' => 'utf8'
          ]
        ],
        [
          'query' => "update articles set status = 0 where userId = 9 ",
          'config' => [
              'driver' => 'MySQLi',
              'host' => 'localhost',
              'port' => 3306,
              'username' => '{other_user}',
              'password' => '{other_pwd}',
              'database' => '{other_db}',
              'charset' => 'utf8'
          ]
        ],
    ];
    ```
    Another example, where all queries will execute in current connection of library.
    
    ```php
    $queries = [
        [
          'query' => "update users set status = 2 where email like '@example.com' "
        ],
        [
          'query' => "update articles set status = 0 where userId = 9 "
        ],
    ];
    ```
    
Return value:

- `array` Result of executed queries

Let's execute a queries in different databases. 

```php
$queries = [
    [
      'query' => "update users set status = 2 where email like '@example.com' ",
      'config' => [
          'driver' => 'MySQLi',
          'host' => 'localhost',
          'port' => 3306,
          'username' => '{user}',
          'password' => '{pwd}',
          'database' => '{db}',
          'charset' => 'utf8'
      ]
    ],
    [
      'query' => "update articles set status = 0 where userId = 9 ",
      'config' => [
          'driver' => 'MySQLi',
          'host' => 'localhost',
          'port' => 3306,
          'username' => '{other_user}',
          'password' => '{other_pwd}',
          'database' => '{other_db}',
          'charset' => 'utf8'
      ]
    ],
];

$queryResults = $this->asyncQueries($queries); 
```

Another example to use library current connection to execute async queries.

```php
$queries = [
    [
      'query' => "update users set status = 2 where email like '@example.com' "
    ],
    [
      'query' => "update articles set status = 0 where userId = 9 "
    ],
];

$queryResults = $this->asyncQueries($queries);
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
    'select * from users where email like ?',
    ['%@example.com']
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
    'select * from users where email = ?',
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
    'email' => 'software@duktig.dev'
];

// fetch row as assoc array by where condition
$result = $this->fetchAssocByWhere('users', $where);
```

The where condition parameters will be converted to query string with "and" operator.

```php
$where = [
   'id' => 55,
   'email' => 'software@duktig.dev',
   'status' => 2 
];

// the query of this method will convert to: select * from users where id = 55 and email = 'software@duktig.dev' and status = '2';
$result = $this->fetchAssocByWhere('users', $where);
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

$query_string = "insert into articles set title = '" . $this->escape($string) . "' ";

$this->query($query);

```

> Notice: In this example we used a query method with escaped string just to demonstrate the functionality.
> With this library it is better to use:
> 
> ```php
> $this->query(
>     "insert into articles set title = ? ", ["test string with 'single' quotes"]); 
> );
> ```    

Another example to use **escape** method

```php
$result = $this->fetchAssoc("show create table `" . $this->escape($tableName) . "`");
```

##### Transactions

MySQLi Library transaction functionality

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
    $this->query("insert into table1 ...");
    $this->query("insert into table2 ...");
    
    // Commit transaction
    $this->commitTrans();
    
} catch(\Throwable $e) {

    // Rollback transaction in case if any of queries execution fail
    $this->rollbackTrans();
    
    // ...
}
```

> See also [Create a **Model** class](../../../app/model.md)

End of document
