# Duktig PHP Microservice - Development Documentation

## Overview - Unit Tests

This document explains how to run, understand, and write unit tests for the Duktig PHP Microservice framework using PHPUnit.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Test Framework](#test-framework)
3. [Test Structure](#test-structure)
4. [Running Tests](#running-tests)
5. [Available Test Suites](#available-test-suites)
6. [Writing New Tests](#writing-new-tests)
7. [Test Examples](#test-examples)
8. [Best Practices](#best-practices)
9. [Troubleshooting](#troubleshooting)

---

## Quick Start

Run all unit tests:

```bash
cd src
./vendor/bin/phpunit --configuration phpunit.xml --testdox
```

Expected output shows test results with documentation format:

```
Lib\Valid
 ✓ Alpha
 ✓ Alpha numeric
 ✓ Email
 ...
```

---

## Test Framework

**Framework**: PHPUnit 9.x+

- Industry-standard PHP testing framework
- Supports data providers, assertions, fixtures
- Code coverage analysis
- Continuous integration ready

**Installation**: Included in composer.json dependencies

---

## Test Structure

Tests are organized by component under `/src/tests/Unit/`:

```
src/tests/
├── bootstrap.php          # Test environment setup
├── Unit/
│   ├── Lib/               # Library tests
│   │   ├── Auth/          # Authentication library tests
│   │   │   ├── JwtTest.php
│   │   │   ├── PasswordTest.php
│   │   │   └── StorageTest.php
│   │   ├── Cache/         # Cache library tests
│   │   ├── Db/            # Database library tests
│   │   ├── Events/        # Event system tests
│   │   ├── HTTP/          # HTTP utilities tests
│   │   ├── BenchmarkingTest.php
│   │   ├── DataGeneratorTest.php
│   │   ├── DataTest.php
│   │   ├── DateTest.php
│   │   ├── LocationByIPTest.php
│   │   ├── ValidTest.php
│   │   └── ValidatorTest.php
│   └── system/            # System component tests
│       └── classes/
└── phpunit.xml            # PHPUnit configuration
```

### Test Naming Convention

- Test files: `{ComponentName}Test.php`
- Test classes: `{ComponentName}Test extends TestCase`
- Test methods: `test_{feature_name}` or `test{FeatureName}`
- Data providers: `{featureName}Provider()`

---

## Running Tests

### Run All Tests

```bash
cd src
./vendor/bin/phpunit --configuration phpunit.xml
```

### Run with Documentation Format

```bash
./vendor/bin/phpunit --configuration phpunit.xml --testdox
```

Shows test names in readable format.

### Run Specific Test File

```bash
./vendor/bin/phpunit tests/Unit/Lib/ValidTest.php
```

### Run Specific Test Method

```bash
./vendor/bin/phpunit --filter test_alpha tests/Unit/Lib/ValidTest.php
```

### Run with Code Coverage

```bash
./vendor/bin/phpunit --configuration phpunit.xml --coverage-html coverage/
```

Generates HTML coverage report in `coverage/` directory.

### Run Tests in Docker

Tests must run in the CLI container (not the PHP-FPM container):

```bash
# Run all tests
docker exec duktig-php-cli-cron ./vendor/bin/phpunit --configuration phpunit.xml --testdox

# Run specific test file
docker exec duktig-php-cli-cron ./vendor/bin/phpunit tests/Unit/Lib/ValidTest.php

# Run with code coverage
docker exec duktig-php-cli-cron ./vendor/bin/phpunit --configuration phpunit.xml --coverage-html coverage/
```

**Note**: Use `duktig-php-cli-cron` container for CLI commands, not `duktig-php-fpm` (which handles HTTP requests only).

---

## Available Test Suites

### Library Tests (Lib)

#### Authentication Tests (`Lib/Auth/`)

- **JwtTest.php** - JSON Web Token encoding, decoding, verification
- **PasswordTest.php** - Password hashing, verification, strength validation
- **StorageTest.php** - Token storage and retrieval

#### Validation Tests (`Lib/`)

- **ValidTest.php** - Data validation rules (alpha, email, URL, etc.)
- **ValidatorTest.php** - Data structure validation

#### Database Tests (`Lib/Db/`)

- Database connection handling
- Query execution
- Result processing

#### Cache Tests (`Lib/Cache/`)

- Cache operations (get, set, delete)
- TTL handling
- Data serialization

#### HTTP Tests (`Lib/HTTP/`)

- Request parsing
- Response handling
- Header management

#### Utility Tests

- **BenchmarkingTest.php** - Performance measurement utilities
- **DataGeneratorTest.php** - Test data generation
- **DataTest.php** - Data manipulation utilities
- **DateTest.php** - Date/time operations
- **LocationByIPTest.php** - IP geolocation services

### System Tests (system/)

- Core framework components
- System initialization
- Configuration loading

---

## Writing New Tests

### Test File Template

Create test file: `src/tests/Unit/Lib/YourComponentTest.php`

```php
<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Lib\YourComponent;

class YourComponentTest extends TestCase
{
    /**
     * Test basic functionality
     */
    public function test_basic_functionality(): void
    {
        $result = YourComponent::doSomething('input');
        
        $this->assertTrue($result);
        $this->assertSame('expected', $result);
    }
    
    /**
     * Test with data provider
     */
    #[DataProvider('dataProvider')]
    public function test_with_multiple_values($input, $expected): void
    {
        $result = YourComponent::process($input);
        $this->assertSame($expected, $result);
    }
    
    /**
     * Data provider for test_with_multiple_values
     */
    public static function dataProvider(): array
    {
        return [
            ['value1', 'result1'],
            ['value2', 'result2'],
            ['value3', 'result3'],
        ];
    }
    
    /**
     * Test exception handling
     */
    public function test_throws_exception_on_invalid_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        YourComponent::doSomething('invalid');
    }
}
```

### Common PHPUnit Assertions

```php
// Equality
$this->assertSame($expected, $actual);      // Strict equality (===)
$this->assertEquals($expected, $actual);    // Loose equality (==)

// Boolean
$this->assertTrue($condition);
$this->assertFalse($condition);

// Type
$this->assertIsString($value);
$this->assertIsArray($value);
$this->assertIsInt($value);

// Array
$this->assertArrayHasKey('key', $array);
$this->assertContains('value', $array);

// String
$this->assertStringContains('substring', 'full string');
$this->assertStringStartsWith('prefix', 'prefix...');

// Null
$this->assertNull($value);
$this->assertNotNull($value);

// Exception
$this->expectException(Exception::class);
$this->expectExceptionMessage('Error message');
```

---

## Test Examples

### Example 1: Valid Library Test

From `tests/Unit/Lib/ValidTest.php`:

```php
<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Lib\Valid;

class ValidTest extends TestCase
{
    #[DataProvider('alphaProvider')]
    public function test_alpha($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::alpha($value));
    }
    
    public static function alphaProvider(): array
    {
        return [
            ['abcXYZ', true],
            ['abc_xyz', false],
            ['abc123', false],
            ['', false],
            [null, false],
        ];
    }

    #[DataProvider('emailProvider')]
    public function test_email($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::email($value));
    }
    
    public static function emailProvider(): array
    {
        return [
            ['user@example.com', true],
            ['invalid.email@', false],
            ['user@domain', false],
        ];
    }
}
```

### Example 2: Authentication Test

From `tests/Unit/Lib/Auth/JwtTest.php` or `PasswordTest.php`:

```php
<?php
namespace Tests\Unit\Lib\Auth;

use PHPUnit\Framework\TestCase;
use Lib\Auth\Password;

class PasswordTest extends TestCase
{
    public function test_password_hashing(): void
    {
        $password = 'secret123';
        $hash = Password::hash($password);
        
        $this->assertNotSame($password, $hash);
        $this->assertTrue(Password::verify($password, $hash));
    }
    
    public function test_wrong_password_fails(): void
    {
        $hash = Password::hash('correct');
        
        $this->assertFalse(Password::verify('wrong', $hash));
    }
}
```

---

## Best Practices

### 1. One Assertion Per Test (When Possible)

Good:
```php
public function test_user_is_created(): void
{
    $user = User::create(['name' => 'John']);
    $this->assertSame('John', $user->name);
}
```

Acceptable (related assertions):
```php
public function test_user_properties(): void
{
    $user = User::create(['name' => 'John', 'email' => 'john@example.com']);
    $this->assertSame('John', $user->name);
    $this->assertSame('john@example.com', $user->email);
}
```

### 2. Use Data Providers for Multiple Scenarios

```php
#[DataProvider('validEmails')]
public function test_email_validation($email): void
{
    $this->assertTrue(Valid::email($email));
}

public static function validEmails(): array
{
    return [
        ['user@example.com'],
        ['test.name@domain.co.uk'],
    ];
}
```

### 3. Test Edge Cases

```php
public function test_with_empty_string(): void { ... }
public function test_with_null(): void { ... }
public function test_with_large_value(): void { ... }
public function test_with_special_characters(): void { ... }
```

### 4. Use Descriptive Test Names

Bad: `test_user()`  
Good: `test_user_creation_with_valid_data()`  
Better: `test_creates_user_with_valid_email_and_name()`

### 5. Test Both Success and Failure Cases

```php
public function test_valid_input_succeeds(): void { ... }
public function test_invalid_input_throws_exception(): void { ... }
public function test_missing_required_field_throws_exception(): void { ... }
```

### 6. Keep Tests Independent

Each test should be able to run independently:

```php
// Bad - relies on test order
public function test_1_create_user(): void { ... }
public function test_2_update_user(): void { ... }

// Good - each test sets up its own state
public function test_update_user(): void
{
    $user = User::create(['name' => 'John']);
    $user->update(['name' => 'Jane']);
    $this->assertSame('Jane', $user->name);
}
```

---

## Troubleshooting

### Issue: Tests Cannot Find Classes

**Cause**: Composer autoload not regenerated

**Solution**:
```bash
cd src
composer dumpautoload
./vendor/bin/phpunit --configuration phpunit.xml
```

### Issue: Bootstrap.php Not Found

**Cause**: Running tests from wrong directory

**Solution**:
```bash
cd src  # Must be in src/ directory
./vendor/bin/phpunit --configuration phpunit.xml
```

### Issue: No Tests Executed

**Cause**: Test file or method naming doesn't follow convention

**Solution**:
- Test files must end with `Test.php`
- Test methods must start with `test_`
- Test classes must extend `TestCase`

### Issue: Tests Fail in Docker but Pass Locally

**Cause**: Environment differences (PHP version, extensions, timezone)

**Solution**:
```bash
# Run tests in Docker container
docker exec duktig-php-fpm sh -c "cd src && ./vendor/bin/phpunit --configuration phpunit.xml"

# Check PHP version in container
docker exec duktig-php-fpm php --version
```

### Issue: Database Tests Fail

**Cause**: Database service not running or wrong credentials

**Solution**:
```bash
# Verify database is running
docker exec duktig-database-mysql mysql -u root -p -e "SELECT 1;"

# Check bootstrap.php uses correct credentials
cat src/tests/bootstrap.php
```

---

## Continuous Integration

For automated testing in CI/CD pipelines, use the CLI container:

```bash
# In CI configuration (GitHub Actions, GitLab CI, etc.)
# Run tests in CLI container
docker exec duktig-php-cli-cron ./vendor/bin/phpunit \
    --configuration phpunit.xml \
    --testdox \
    --coverage-text \
    --coverage-clover coverage.xml
```

**Important**: Always use `duktig-php-cli-cron` container for test execution, not `duktig-php-fpm`.

---

## Related Documentation

- [Getting Started](getting-started.md) - Framework overview
- [Coding Standards](coding-standards.md) - Code quality standards
- PHPUnit Documentation: https://phpunit.de/documentation.html
- [Requirements](requirements.md) - System requirements
