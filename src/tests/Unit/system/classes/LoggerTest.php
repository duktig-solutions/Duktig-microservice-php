<?php
namespace Tests\Unit\system\classes;

use PHPUnit\Framework\TestCase;
use System\Logger;

class LoggerTest extends TestCase
{
    public function test_logger_logging_and_clean(): void
    {
        // The Logger class exposes Logger::Log(type, message, context?)
        $this->assertNull(Logger::Log('info', 'message'));
        $this->assertNull(Logger::Log('error', 'oops', 'error.log'));

        Logger::CleanLogFile('app.log'); // should not throw
        $this->assertTrue(true);
    }
}
