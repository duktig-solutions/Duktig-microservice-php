<?php
namespace Tests\Unit\Lib\Events;

use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Events\Event'), 'Class not found: \Lib\Events\Event');
    }
}
