<?php
namespace Tests\Unit\Lib\Auth;

use PHPUnit\Framework\TestCase;

final class PasswordTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Auth\Password'), 'Class not found: \Lib\Auth\Password');
    }
}
