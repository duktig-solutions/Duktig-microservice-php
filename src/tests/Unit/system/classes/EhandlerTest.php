<?php
namespace Tests\Unit\system\classes;

use PHPUnit\Framework\TestCase;

final class EhandlerTest extends TestCase
{
    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists('\System\Ehandler'), 'Class not found: \System\Ehandler');
    }
}
