# MySQLiUtility

MySQL Database Utility class library

**MySQLiUtility** class extends [**MySQLi**](mysqli.md) library and your models can extend it as explained in [MySQLi](mysqli.md) library documentation. 

> We recommend to create a model and extend it to **MySQLiUtility** to use MySQLi Utility methods, 
> however, it is possible to define **MySQLiUtility** instance in any class and use as shown bellow.

```php
// Assume this code will work in controller or middleware class 

// get database connection configuration
$databaseConfig = \System\Config::get()['Databases']['my_database'];

// define MySQLiUtility object
$utilityLib = new \Lib\Db\MySQLiUtility($databaseConfig);

// get tables as array
$tables = $utilityLib->getTables();			    
```  

> NOTE: Because Database models extends this class library, all example codes listed bellow will use `$this->` which means the model instance. 

##### Methods

- [getTables() - Get database tables](#get-tables)
- [getCreateTable() - Get create table syntax](#get-create-table)

###### Get tables

Get list of database tables as an array

`array getTables()`

Arguments:

- No arguments

Return value:

- `array` List of database tables

```php
// get list of database tables
$tablesList = $this->getTables();
```
###### Get create table

Get **CREATE TABLE** syntax for given table

`string getCreateTable(string $tableName)`

Arguments:

- `string` Table name

Return value:

- `string` Create table syntax

```php
// get create table syntax
$createTableSyntax = $this->getCreateTable('users');
```

> See also [Create a **Model** class](../../../app/model.md)

End of document