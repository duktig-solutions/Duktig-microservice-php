<?php
namespace Tests\Unit\Lib\HTTP;

use PHPUnit\Framework\TestCase;

final class ClientInfoTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\HTTP\ClientInfo'), 'Class not found: \Lib\HTTP\ClientInfo');
    }
}
