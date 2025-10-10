<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use Lib\Date;

class DateTest extends TestCase
{
    public function test_time_elapsed_string(): void
    {
        $past = time() - 3600;
        $res = Date::timeElapsedString($past);
        $this->assertIsString($res);
        $this->assertNotSame('', $res);
    }

    public function test_dates_intersects(): void
    {
        $this->assertTrue((bool) Date::datesIntersects('2025-01-01','2025-01-10','2025-01-05','2025-01-06'));
        $this->assertFalse((bool) Date::datesIntersects('2025-01-01','2025-01-10','2025-01-11','2025-01-12'));
    }
}
