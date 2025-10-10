<?php
namespace Tests\Unit\system\classes\Events;

use PHPUnit\Framework\TestCase;

final class SubscriberTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\System\Events\Subscriber'), 'Class not found: \System\Events\Subscriber');
    }
}
