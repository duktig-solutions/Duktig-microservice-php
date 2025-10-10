<?php
namespace Tests\Unit\Lib\Db;

use PHPUnit\Framework\TestCase;

final class MySQLiTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Db\MySQLi'), 'Class not found: \Lib\Db\MySQLi');
    }
}
