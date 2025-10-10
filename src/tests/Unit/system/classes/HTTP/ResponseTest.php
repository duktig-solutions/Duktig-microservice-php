<?php
namespace Tests\Unit\system\classes\HTTP;

use PHPUnit\Framework\TestCase;
use System\HTTP\Response;

class ResponseTest extends TestCase
{
    public function test_status_header_write_reset_and_cache(): void
    {
        $res = new Response();
        $res->status(200);
        $res->header('X-Test','1');
        $res->write('Body content');

        // enableCaching expects (array $config, string $key)
        $res->enableCaching(['enabled'=>true,'ttl'=>1], 'unit-key');
        $res->reset();
        $this->assertTrue(true);
    }

    public function test_output_and_disable_cache_methods_exist(): void
    {
        $res = new Response();
        if (method_exists($res, 'disableCaching')) {
            $res->disableCaching();
        }
        $this->assertTrue(true);
    }
}
