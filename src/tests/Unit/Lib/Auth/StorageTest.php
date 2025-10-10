<?php
namespace Tests\Unit\Lib\Auth;

use PHPUnit\Framework\TestCase;

final class StorageTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\Lib\Auth\Storage'), 'Class not found: \Lib\Auth\Storage');
    }
}
