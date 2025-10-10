<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use Lib\Data;

class DataTest extends TestCase
{
    public function test_match_wildcard(): void
    {
        $this->assertTrue((bool) Data::matchWildcard('user_*', 'user_123'));
        $this->assertFalse((bool) Data::matchWildcard('order_*', 'user_123'));
    }

    public function test_format_id_str(): void
    {
        $this->assertSame('123', Data::formatIdStr(123, 6));
    }

    public function test_title_to_url(): void
    {
        $this->assertSame('hello-world', Data::titleToURL('Hello World'));
    }
}
