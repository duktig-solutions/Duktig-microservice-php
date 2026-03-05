# Duktig PHP Microservice - Development Documentation

## Coding Standards

The **Duktig PHP Framework** project provides preferred coding standards and rules for developers to use.

> Note: Your code reflects your technical proficiency and quality of work!

## Files Creation Rules

- Save with Unicode (UTF-8) encoding without BOM
- Start name with uppercase
- Write name in Camel Case
- Name does not contain any symbols

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

These rules apply to almost all files, such as Controllers, Middleware classes, Models, Libraries, and others. 

## PHP Tags Creation Rules

In the Duktig PHP Framework, it is very important to follow PHP tags creation rules. Depending on server PHP configuration, opening tags without `php` can cause unexpected behavior.

- Strongly required to start PHP tags with `<?php`
- Recommended to omit PHP closing tags `?>`

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

## Code Comments Creation Rules

In the Duktig PHP Framework, it is strongly recommended to comment all parts of code as detailed as possible.

For files, classes, methods, and other DocBlocks, see: [Anatomy of a DocBlock](https://docs.phpdoc.org/guides/docblocks.html).

*If you code with detailed comments, you respect other developers.*

- Classes, methods, and other parts of code should be commented with DocBlock
- All single-line comments should start with `#` symbol

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

## Constants Definition Rules

You can define constants for the Duktig PHP Framework project in the file: `app/config/constants.php`

- Constants can be defined only in the file: `app/config/constants.php`
- All constants should be defined in UPPERCASE only
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

In the Duktig PHP Framework, follow these rules when creating logical operations:

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

## Class Definition Rules

In the Duktig PHP Framework, all types of classes are organized similarly but divided by namespaces. For instance, you can have a class `User` in the Controller namespace as a controller and a class `User` in the model namespace as a model.

- All class names should equal the file names
- Class names should start with uppercase
- Class names should be written in Camel Case
- Each class file should contain only **one** class

If the file name is `User.php`, then the class inside the file should be named **User**.

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
// Class name should equal the file name
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

// Another class in the same file
class AnotherClass {
    // ...
}
```

## Classes Methods Creation Rules

In any project, method names explain the nature of functionality. Therefore, they should be more accurate and readable.

- Start with lowercase
- Written in Camel Case
- Do not contain any symbols
- Names should be readable for other developers
- Provide type hints in function definition
- Default arguments should be on the right side

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

## Type Hints

Because this project requires PHP version 7.4 or higher, it is strongly recommended to define methods with type hints for arguments. Example:

```php
<?php

// Method requires integer type for first argument, string type for second argument, and returns an array
function myFunc(int $a, string $b) : array {
    // ...
}
```

Possible type hints:

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

## Variables Definition Rules

In the Duktig PHP Framework, variable names should be defined as readable as possible.

- Start with lowercase
- Written in Camel Case
- Do not contain any symbols
- Names should be defined as readable as possible
- Single-letter variables should only be used in for() loops

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

Rules for logical operations and loops:

- Logical operations and loops should be defined with curly braces
- Variable names in `foreach()` should be defined readably instead of `$key => $value`
- Logical operations with more than one condition should be defined with `elseif` and `else` for clarity and control
- Early validation and exit conditions should be placed at the beginning of the method body

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

# Logical operations with more than one condition should be defined with elseif and else
if($a == 10) {
	// ...
} elseif($a == 99) {
	// ...
} elseif($a == 110) {
	// ...
} else {
	// ...
}

# Variable names in foreach() should be defined readably instead of $key => $value
foreach($users as $userId => $userRow) {
	echo $userRow['name'];
}

# Early validation and exit conditions should be at the beginning of the method body
function testMethod(string $name, int $age) : bool {
	
	# Check all exceptional cases first
	if($age < 18) {
		return false;
	}

	if($name == '') {
		return false;
	}

	# Function body here
	$nameStr = $name . ' ' . $age;
	
	// ...
	
}
```

**Incorrect**

```php
<?php
# Logical operations and loops should be defined with curly braces
if($a > 90)
	echo $a;

for($i = 0; $i <= 10; $i++)
	echo $i;

$counter = 0;
while($counter < 10)
	$counter++;

# Multiple conditions should use elseif, not multiple if statements
if($a == 10) {
	// ...
}
if($a == 99) {
	// ...
}
if($a == 110) {
	// ...
}

# Variable names in foreach should be descriptive
foreach($users as $key => $value)
	echo $value['name'];

# Poor approach: entire function body nested in single condition
function testMethod(string $name, int $age) : bool {
	
	$nameStr = '';
	
	if($age >= 18 and $name != '') {
		
		$nameStr = $name . ' ' . $age;
		
		// ...
		
	} else {
		return false;
	}	
}
```

## Other Coding Standards

Please follow the additional coding standards listed below:

**Correct**

```php
<?php
/**
 * Example class
 *
 * @author My Name <myName@example.com>
 * @version 1.0.2
 */
class MyClass {
    
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
        
        # Define result as an empty array
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
// if( !empty($c)) {
//     $result[] = $c;
// }
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
