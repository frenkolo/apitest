<?php
require './vendor/autoload.php';
use Slim\Http\Environment;
use Slim\Http\Request;

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected $app;

    public function setUp() : void
    {
        $this->app = (new App())->get();
    }

    public function testTodoGet()
    {
        $env = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $this->assertSame((string)$response->getBody(), "Hello, Todo");
    }
}