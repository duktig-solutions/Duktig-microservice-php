<?php
namespace Tests\Unit\Lib\HTTP;

use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\HTTP\Client'), 'Class not found: \Lib\HTTP\Client');
    }
}
