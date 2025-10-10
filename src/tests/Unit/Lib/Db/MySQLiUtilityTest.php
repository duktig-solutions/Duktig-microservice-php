<?php
namespace Tests\Unit\Lib\Db;

use PHPUnit\Framework\TestCase;

final class MySQLiUtilityTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Db\MySQLiUtility'), 'Class not found: \Lib\Db\MySQLiUtility');
    }
}
