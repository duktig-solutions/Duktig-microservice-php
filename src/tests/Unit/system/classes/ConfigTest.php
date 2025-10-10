<?php
namespace Tests\Unit\system\classes;

use PHPUnit\Framework\TestCase;
use System\Config;

class ConfigTest extends TestCase
{
    public function test_get_app_config_returns_array_with_expected_keys(): void
    {
        $cfg = Config::get('app');
        $this->assertIsArray($cfg);
        $this->assertArrayHasKey('ProjectName', $cfg);
        $this->assertArrayHasKey('Mode', $cfg);
        $this->assertArrayHasKey('DateTimezone', $cfg);
    }
}
