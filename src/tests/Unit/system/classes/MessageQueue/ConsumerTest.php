<?php
namespace Tests\Unit\system\classes\MessageQueue;

use PHPUnit\Framework\TestCase;

final class ConsumerTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\System\MessageQueue\Consumer'), 'Class not found: \System\MessageQueue\Consumer');
    }
}
