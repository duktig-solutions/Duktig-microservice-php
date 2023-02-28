# Auth / Password

**Password** library in Duktig PHP Framework supports functionality to **encrypt**, **verify** password and more.

File: `kernel/lib/auth/Password.php`
Class: `Password`

##### Methods

- [encrypt() - Encrypt password](#encrypt-password)
- [verify() - Verify password](#verify-password)
 
###### Encrypt password

Encrypt password with default algorithm.

`static string encrypt(string $password)`

Arguments:

- `string` password to encrypt

Return value:

- `string` Encrypted password

```php
$myPassword = \Lib\Auth\Password::encrypt('abc@123!');

// Will return a string like: $2y$10$/VAIP5Cv1ejkxWTcIgEl2ucdkKZPJHxAaez8RG/DuobpgTEBxWPam 
```

###### Verify password

`static bool verify(string $password, string $hash)` 

Arguments:

- `string` password to verify
- `string` encrypted password

Return value:

- `bool` **True** in case if verified
- `bool` **False** in case if not verified 

```php

$dbHashedPassword = '$2y$10$/VAIP5Cv1ejkxWTcIgEl2ucdkKZPJHxAaez8RG/DuobpgTEBxWPam';
$requestedPassword = 'abc@123!$';

if(\Lib\Auth\Password::verify($requestedPassword, $dbHashedPassword)) {
    echo 'Password verified';    
} else {
    echo 'Password not verified';
}
```

End of document