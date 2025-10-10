<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;

final class DataGeneratorTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\DataGenerator'), 'Class not found: \Lib\DataGenerator');
    }
}
