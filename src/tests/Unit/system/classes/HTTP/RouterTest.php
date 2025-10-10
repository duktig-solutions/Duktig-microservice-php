<?php
namespace Tests\Unit\system\classes\HTTP;

use PHPUnit\Framework\TestCase;
use System\HTTP\Router;

class RouterTest extends TestCase
{
    public function test_construct_router_with_routes(): void
    {
        $routes = [
            '/' => ['controller'=>'Home','action'=>'index'],
            '/users/{id}' => ['controller'=>'Users','action'=>'show']
        ];
        $r = new Router($routes);
        $this->assertTrue(true);
    }
}
