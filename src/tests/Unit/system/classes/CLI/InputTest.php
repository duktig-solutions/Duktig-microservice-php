<?php
namespace Tests\Unit\system\classes\CLI;

use PHPUnit\Framework\TestCase;

final class InputTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\System\CLI\Input'), 'Class not found: \System\CLI\Input');
    }
}
