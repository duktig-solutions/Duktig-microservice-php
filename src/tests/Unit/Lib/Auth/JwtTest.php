<?php
namespace Tests\Unit\Lib\Auth;

use PHPUnit\Framework\TestCase;

final class JwtTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Auth\Jwt'), 'Class not found: \Lib\Auth\Jwt');
    }
}
