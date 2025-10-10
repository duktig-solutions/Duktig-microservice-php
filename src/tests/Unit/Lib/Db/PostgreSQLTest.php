<?php
namespace Tests\Unit\Lib\Db;

use PHPUnit\Framework\TestCase;

final class PostgreSQLTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Db\PostgreSQL'), 'Class not found: \Lib\Db\PostgreSQL');
    }
}
