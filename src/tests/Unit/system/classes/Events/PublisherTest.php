<?php
namespace Tests\Unit\system\classes\Events;

use PHPUnit\Framework\TestCase;

final class PublisherTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\System\Events\Publisher'), 'Class not found: \System\Events\Publisher');
    }
}
