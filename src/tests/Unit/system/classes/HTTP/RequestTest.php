<?php
namespace Tests\Unit\system\classes\HTTP;

use PHPUnit\Framework\TestCase;
use System\HTTP\Request;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_GET = ['limit' => '2', 'count' => '22'];
        $_POST = [];
        $_FILES = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/users/15/posts/69?limit=2&count=22';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token123';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTPS'] = 'off';
    }

    public function test_basic_accessors(): void
    {
        $req = new Request();
        $this->assertSame('GET', $req->method());
        $this->assertStringContainsString('/users/15/posts/69?limit=2&count=22', $req->uri());
        $this->assertSame(['users','15','posts','69'], $req->paths());

        $this->assertSame('2', $req->get('limit'));
        $this->assertSame('22', $req->get('count'));
        $this->assertSame('', $req->get('missing'));

        $headers = $req->headers();
        $this->assertArrayHasKey('X-Requested-With', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertSame('XMLHttpRequest', $headers['X-Requested-With']);
        $this->assertSame('Bearer token123', $headers['Authorization']);

        $this->assertTrue($req->isAjax());
    }

    public function test_raw_input_is_string(): void
    {
        $req = new Request();
        $this->assertIsString($req->rawInput());
    }
}
