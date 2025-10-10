<?php
namespace Tests\Unit\system\classes\MessageQueue;

use PHPUnit\Framework\TestCase;

final class HealthInspectorTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\System\MessageQueue\HealthInspector'), 'Class not found: \System\MessageQueue\HealthInspector');
    }
}
