<?php
namespace Tests\Unit\Lib\Cache;

use PHPUnit\Framework\TestCase;

final class RedisTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Cache\Redis'), 'Class not found: \Lib\Cache\Redis');
    }
}
