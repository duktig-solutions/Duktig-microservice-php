<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Lib\Valid;

class ValidTest extends TestCase
{
    #[DataProvider('alphaProvider')]
    public function test_alpha($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::alpha($value));
    }
    public static function alphaProvider(): array
    {
        return [
            ['abcXYZ', true],
            ['abc_xyz', false],
            ['abc123', false],
            ['', false],
            [null, false],
        ];
    }

    #[DataProvider('alphaNumericProvider')]
    public function test_alpha_numeric($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::alphaNumeric($value));
    }
    public static function alphaNumericProvider(): array
    {
        return [
            ['abc123', true],
            ['ABC-xyz_123', true],
            ['abc 123', false],
            ['$', false],
            [[], false],
        ];
    }

    #[DataProvider('emailProvider')]
    public function test_email($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::email($value));
    }
    public static function emailProvider(): array
    {
        return [
            ['user@example.com', true],
            ['USER+tag@sub.domain.io', true],
            ['bad@domain', false],
            ['not-an-email', false],
            [null, false],
        ];
    }

    #[DataProvider('urlProvider')]
    public function test_url($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::url($value));
    }
    public static function urlProvider(): array
    {
        return [
            ['http://example.com', true],
            ['https://example.com/path?q=1', true],
            ['ftp://example.com', true],
            ['example', false],
            ['', false],
        ];
    }

    #[DataProvider('ipProvider')]
    public function test_ip_address($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::ipAddress($value));
    }
    public static function ipProvider(): array
    {
        return [
            ['127.0.0.1', true],
            ['::1', false],
            ['256.256.256.256', false],
            ['bad.ip', false],
        ];
    }

    #[DataProvider('digitsProvider')]
    public function test_digits($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::digits($value));
    }
    public static function digitsProvider(): array
    {
        return [
            ['12345', true],
            ['0123', true],
            ['12a', false],
            ['', false],
        ];
    }

    #[DataProvider('intRangeProvider')]
    public function test_int_range($value, $min, $max, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::intRange($value, $min, $max));
    }
    public static function intRangeProvider(): array
    {
        return [
            [5, 1, 10, true],
            [1, 1, 10, true],
            [10, 1, 10, true],
            [0, 1, 10, false],
            [11, 1, 10, false],
        ];
    }

    #[DataProvider('floatRangeProvider')]
    public function test_float_range($value, $min, $max, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::floatRange($value, $min, $max));
    }
    public static function floatRangeProvider(): array
    {
        return [
            [1.5, 1.0, 2.0, true],
            [1.0, 1.0, 2.0, true],
            [2.0, 1.0, 2.0, true],
            [0.99, 1.0, 2.0, false],
            [2.01, 1.0, 2.0, false],
        ];
    }

    #[DataProvider('dateIsoProvider')]
    public function test_date_iso($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::dateIso($value));
    }
    public static function dateIsoProvider(): array
    {
        return [
            ['2025-10-10', true],
            ['1999-01-01', true],
            ['10/10/2025', false],
            ['2025-13-01', false],
        ];
    }

    #[DataProvider('dateTimeIsoProvider')]
    public function test_date_time_iso($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::dateTimeIso($value));
    }
    public static function dateTimeIsoProvider(): array
    {
        return [
            ['2025-10-10 12:30:45', true],
            ['2025-10-10T12:30:45', false],
            ['10-10-2025 12:30:45', false],
        ];
    }

    #[DataProvider('phoneProvider')]
    public function test_phone_e164($value, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::phoneNumberE164($value));
    }
    public static function phoneProvider(): array
    {
        return [
            ['+37499123456', true],
            ['+1-202-555-0123', false],
            ['0037499123456', false],
            ['abc', false],
        ];
    }

    #[DataProvider('stringLengthProvider')]
    public function test_string_length($value, $min, $max, $expected): void
    {
        $this->assertSame($expected, (bool) Valid::stringLength($value, $min, $max));
    }
    public static function stringLengthProvider(): array
    {
        return [
            ['abc', 1, 5, true],
            ['a', 2, 5, false],
            ['abcdef', 1, 5, false],
        ];
    }

    public function test_latitude_longitude(): void
    {
        $this->assertTrue((bool) Valid::latitude(40.1772));
        $this->assertFalse((bool) Valid::latitude(100));
        $this->assertTrue((bool) Valid::longitude(44.50349));
        $this->assertFalse((bool) Valid::longitude(200));
    }
}
