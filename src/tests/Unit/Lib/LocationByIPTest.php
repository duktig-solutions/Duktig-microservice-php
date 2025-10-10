<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;

final class LocationByIPTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\LocationByIP'), 'Class not found: \Lib\LocationByIP');
    }
}
