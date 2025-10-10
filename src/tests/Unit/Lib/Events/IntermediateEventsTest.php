<?php
namespace Tests\Unit\Lib\Events;

use PHPUnit\Framework\TestCase;

final class IntermediateEventsTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Events\IntermediateEvents'), 'Class not found: \Lib\Events\IntermediateEvents');
    }
}
