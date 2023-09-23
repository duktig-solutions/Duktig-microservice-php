# Duktig PHP Microservice - Development documentation

## Coding Standards

**Duktig PHP Framework** Project provides preferred coding standards and rules for developers to use.

>NOTE: Your code talks about your skills and quality of work!

## Files creation rules 

- Save with Unicode (UTF-8) encoding without BOM. 
- Start name with uppercase
- Write name in Camel Case
- Name **Not** contains any symbol 

**Correct**

    User.php
    UserNotifications.php
    UserStatsData.php
    
**Incorrect**

    user.php
    userNotifications.php
    userstatsdata.php
    user-stats-data.php
    user_stats_data.php
    User.class.php

This rules affects almost to all files, such as `Controllers`, `Middleware classes`, `Models`, `Libraries` and other. 

## PHP Tags creation rules

In Duktig PHP Framework it is very important to follow PHP tags creation rules. 
Depends on server PHP configuration, the opening tags without `php` can cause an unexpected behavior.  

- Strongly required to start PHP tags containing **"php"**: `<?php`:
- Recommended to skip PHP closing tags `?>`.

**Correct**

```php
<?php
// Your code here
// Another code here

// End of file
```

**Incorrect**

```php
<?
// Your code here
// Another code here

// End of file
?>
```

## Code Comments creation rules

In Duktig PHP Framework it is strongly recommended to comment all parts of code as detailed as possible.

For Files, Classes, methods and other DocBlocks see: [Anatomy of a DocBlock](https://docs.phpdoc.org/guides/docblocks.html).

*If you are coding with detailed comments, you respect other developers*

 
- Classes, Methods and other parts of code should be commented with DocBlock.
- All Single line comments should start with `#` symbol.

**Correct**

```php
<?php
/**
 * Class User Model to work with Database Records
 *
 * @author Author Name <author@example.com>
 * @license see License.md 
 * @version 1.0.5 
 */

/**
 * Class User
 *
 * @package App\Models
 */
class User {
	
	/**
     * Users table name in Database
     *
     * @access private
     * @var string  
     */
	private $table;
	
	/**
     * Class Constructor
     */
	public function __construct() {
		$this->table = 'users';
	}
	
    /**
     * Fetch limited user rows from Database by where condition 
     * 
     * @access public
     * @param array $where
     * @param int $limit = 10
     * @return array 
     */
	public function fetchRows(array $where, ?int $limit = 5) : array {
		
		# Check if where condition
		if(!empty($where)) {
			// Do something ...
		}
		
		# Define an example result
		# Just to demonstrate values
		$result = [
            [
                'id' => 5,
                'name' => 'David'            	
            ],
            [
            	'id' => 6,
            	'name' => 'Jacob'
            ]
        ];
		
		return $result;
		
	}
	
}
```

**Incorrect**

```php
<?php
/* Class User Model */

// Class User
class User {
	
	# table name
	private $table;
	
	public function __construct() {
		$this->table = 'users';
// something commented not correct here		
	}
	
	// get records
	public function fetchRows(array $where, ?int $limit = 5) : array {
		
		if(!empty($where)) {
			// Do something ...
		}
		
		$result = [
            [
                'id' => 5,
                'name' => 'David'            	
            ],
            [
            	'id' => 6,
            	'name' => 'Jacob'
            ]
        ];
		
		return $result;
		
	}
	
}
```

## Constants definition rules

You can define constants for Duktig PHP Framework project in file: `app/config/constants.php`


- Constants can be defined only in file: `app/config/constants.php`
- All constants should be defined only in UPPER CASE
- Words in names can be divided by underscore: `_`
- Names should be defined as readable as possible

**Correct**

```php
<?php
define('MY_NAME', 'David');
define('USER_STATUS_ACTIVE', 1);
define('USER_ALLOW_CREATE_ARTICLE', true);
```

**Incorrect**

```php
<?php
define('MYNAME', 'David');
define('myname', 'David');
define('my_name', 'David');
define('user_status_active', 1);
define('User_Allow_Create_Article', true);
define('USERALLOWCREATEARTICLE', true);
define('USCRTARTL', true);
```

## Logical Operations

In Duktig PHP Framework there are several rules to follow when creating logical operations.   

**Correct**

```php
<?php
if($a == $b and $k != $b)
if($a > 0 or $a < 100)
if(!empty($a))
if(is_null($a))
``` 

**Incorrect**

```php
<?php
if($a == $b && $k != $b)
if($a > 0 || $a < 100)
if($a)
if(!$a)
``` 

## Class definition rules 

In Duktig PHP Framework all type of classes are almost similar divided by namespaces.
As an instance, you can have class `User` in Controller namespace as a controller and class `User` in model namespace as a model.

- All class names should be equal to file names
- Class names should start with upper case 
- Class names should be written in camel case
- Each file of class should contain only its own **One** class.

If the File name defined as `User.php`, than the class inside file should be defined as **User**.

**Correct** 

File: `User.php`

```php
<?php
class User {
    // ...
}
```

File: `UserNotifications.php`

```php
<?php

class UserNotifications {
    // ...
}
```

**Incorrect**

File: `User.php`

```php
<?php

// Class name should be equal to file name
class UserClass {
    // ...
}
```

File: `UserNotifications.php`

```php
<?php

class UserNotifications {
    // ...
}

// Another class inside file
class AnotherClass {
    // ...
}
```

## Classes methods creation rules

In any project, method names explains the mater of functionality. So they should be more accurate and readable.


- start with lower case
- written as Camel case
- Not contain any symbol
- Name should be readable for other developers
- Provide type hints in function definition
- Default arguments should be on right side

**Correct**
    
```php
<?php

function parse()
function parseNotifications()
function parseUserNotifications()

```
        
**Incorrect**
    
```php
<?php

function Parse()
function PARSE()
function ParseUserNotifications()
function Parse_User_Notifications()
function parse_user_notifications()
function parseusernotifications()
function prsusrntf()
```

## Type hints

Because this project requires PHP version starting from `7.4`, it is strongly recommended to define methods with type hints for arguments. Example:

```php
<?php

// Method requires integer type for first argument, string type for second argument and returns an array.
function myFunc(int $a, string $b) : array {
    // ...
}
``` 

Possible type hints

**array**

```php
<?php

// array
function foo(array $a)
function foo(?array $a)
function foo(array $a): array
function foo(array $a): ?array
function foo(): ?array
```

**bool**

```php
<?php

// bool
function foo(bool $bar)	
function foo(?bool $bar)
function foo(bool $bar = null)
function foo(): bool
function foo(): ?bool
function foo(bool $bar): ?bool
```

**callable**

```php
<?php

// callable
function foo(callable $bar)
function foo(?callable $bar)	
function foo(callable $bar = null)
function foo(): callable
function foo(): ?callable
function foo(callable $bar): ?callable
```

**float**

```php
<?php

// float
function foo(float $bar)
function foo(?float $bar)
function foo(float $bar = null)
function foo(): float
function foo(): ?float
function foo(float $bar): ?float
```

**int**

```php
<?php

// int
function foo(int $bar)
function foo(?int $bar)
function foo(int $bar = null)
function foo(): int	
function foo(): ?int
function foo(int $bar): ?int
```

**iterable**

```php
<?php

// iterable
function foo(iterable $bar)
function foo(?iterable $bar)
function foo(iterable $bar = null)
function foo(): iterable
function foo(): ?iterable
function foo(iterable $bar): ?iterable
```

**object (PHP 7.2+)**

```php
<?php

// object - PHP 7.2+
function foo(object $bar)
function foo(?object $bar)
function foo(object $bar = null)
function foo(): object
function foo(): ?object
function foo(object $bar): ?object 
```

**self**

```php
<?php

// self
function foo(self $bar)
function foo(?self $bar)
function foo(self $bar = null)
function foo(): self
function foo(): ?self
function foo(self $bar): ?self
```

**string**

```php
<?php

// string
function foo(string $bar)
function foo(?string $bar)
function foo(string $bar = null)
function foo(): string
function foo(): ?string
function foo(string $bar): ?string
```

**class name**

```php
<?php

// class names
function foo(ClassName $bar)
function foo(?ClassName $bar)
function foo(ClassName $bar = null)
function foo(): ClassName
function foo(): ?ClassName
function foo(ClassName $bar): ?ClassName
```

**void**

```php
<?php

// void
function foo(): void
function bar(int $a, string $b): void
```

## Default arguments

Note that when using default arguments, any defaults should be on the right side of any non-default arguments; otherwise, things will not work as expected.

**Correct**

```php
<?php

function myFunc(int $a, int $b, int $c = 5, int $d = 9) : int {
    // ...
}
```

**Incorrect**

```php
<?php

function myFunc(int $c = 5, int $d = 9, int $a, int $b) : int {
    // ...
}

function myFunc(int $c = 5, int $d, int $a = 4, int $b) : int {
    // ...
}
```

## Variables definition rules

In Duktig PHP Framework variable names should be defined much readable as it's possible.


- Start with lower case
- Written as Camel case
- Not contain any symbol
- Names defined as readable as possible
- Single letter variables should only be used in for() loops

**Correct**
    
```php
<?php

$name = 'David';
$fullName = 'David A.';
$userLastStatus = 0;
```

**Incorrect**
    
```php
<?php

$Name = 'David';
$FullName = 'David A.';
$UserLastStatus = 0;
$userlaststatus = 0;
$user_Last_Status = 0;
$user_LastStatus = 0;
$uslst = 0;
$u = 0;
```

## IF, ELSE, ELSEIF, FOR, FOREACH, WHILE

Rules for logical operations and loops

- Logical operations and loops should be defined with curly braces 
- Variable names in `foreach()` should be defined readable instead of `$key => $value`
- Logical operations with more than one condition should be defined with `elseif` and `else` to take control on.
- Logical operations to except something in method should be defined in top of body
 
**Correct**

```php
<?php

# Logical operations and loops should be defined with curly braces
if($a > 90) {
	echo $a;
}

for($i = 0; $i <= 10; $i++) {
	echo $i;
}

$counter = 0;

while($counter < 10) {
	$counter++;
}

# Logical operations with more than one condition should be defined with `elseif` and `else` to take control on.
if($a == 10) {
	// ...
} elseif($a == 99) {
	// ... 
} elseif($a == 110) {
	// ...
} else {
	// ...
}

# Variable names in `foreach()` should be defined readable instead of `$key => $value`
foreach($users as $userId => $userRow) {
	echo $userRow['name'];
}

# Logical operations to except something in method should be defined in top of body
function testMethod(string $name, int $age) : bool {
	
	# For the first, check all excepted cases in top of body
	if($age < 18) {
		return false;
	}

    if($name == '') {
		return false;
    }
	
    # This is also possible to do in one line. just for example, we written different conditions
    # if($name < 18 or $name == '')
    
	# Function body here
	$nameStr = $name . ' ' . $age;
	
	// ...
	
}
```

**Incorrect**

```php
<?php
// Logical operations and loops should be defined with curly braces
if($a > 90)
	echo $a;

for($i = 0; $i <= 10; $i++) 
	echo $i;


$counter = 0;
while($counter < 10) 
	$counter++;

// Logical operations with more than one condition should be defined with `elseif` and `else` to take control on.
if($a == 10) {
	// ...
} 
if($a == 99) {
	// ... 
} 
if($a == 110) {
	// ...
} 

// Variable names in `foreach()` should be defined readable instead of `$key => $value`
foreach($users as $key => $value)
	echo $value['name'];

// Logical operations to except something in method should be defined in top of body
// This is terrible approach to write a whole function body in one logical condition
function testMethod(string $name, int $age) : bool {
	
	$nameStr = '';
	
	// First check the age
	if($age >= 18 and $name != '') {
		
		// Function body here
		$nameStr = $name . ' ' . $age;
		
		// ...
		
	} else {
		return false;
	}	
}

```

## Other coding standards

Please follow to other coding standards listed bellow: 

**Correct**

```php
<?php
/**
 * Example class 
 * 
 * @author My Name <myName@example.com>
 * @version 1.0.2 
 */
class myClass { 
    
    /**
     * @access private 
     * @var int
     */
    private $level;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->level = 5;
    } 
     
    /**
     * Return incrementation of values
     * 
     * @access public
     * @param int $a
     * @param string $b
     * @param mixed $c
     * @return array
     */
    public function myFunc(int $a, string $b, ?string $c) : array {
        
        # Define result as an empty array.
        $result = [];
        
        # Increment the array 
        for($i = 0; $i <= $a; $i++) {
            $result[$i] = $b;
        }
    
        # Append another element if not null
        if(!is_null($c)) {
            $result[] = $c;
        }
        
        return $result;
        
    }
     
}    
```

**Incorrect**

```php
<?php
class myclass {
    var $L;
    // add values
    function my_func ( int $a,string $b,?string $c):array 
    {
        
        // result
        $result = array();
        
        for($i=0;$i<=$a;$i++) 
        {
            $result[ $i ] = $b;
        }
//if( !empty($c)) {
//    $result[] = $c;
//}    
        if(!$c)
           $result[] = $c; // not null
                
        return $result;
        
    }
    
    public function __construct() 
    {
        $this->L = 55;
    } 
}   
?>
```
