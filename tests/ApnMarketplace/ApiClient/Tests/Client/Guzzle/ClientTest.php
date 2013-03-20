<?php

namespace ApnMarketplace\ApiClient\Tests\Client\Guzzle;

use ApnMarketplace\ApiClient\Client\Guzzle\Client;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;
use ApnMarketplace\ApiClient\HttpResponse;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $client = new Client();
        $mock = new MockPlugin();
        $mock->addResponse(new Response(200, array('Foo' => 'bar'), 'body'));
        $client->addSubscriber($mock);

        $expected = new HttpResponse("HTTP/1.1 200 OK\r\nFoo: bar\r\n\r\nbody");
        $this->assertEquals($expected, $client->get('http://www.example.com/'));
    }

    public function testPost()
    {
        $client = new Client();
        $mock = new MockPlugin();
        $mock->addResponse(new Response(200, array('Foo' => 'bar'), 'body'));
        $client->addSubscriber($mock);

        $expected = new HttpResponse("HTTP/1.1 200 OK\r\nFoo: bar\r\n\r\nbody");
        $this->assertEquals($expected, $client->post('http://www.example.com/'));
    }

    public function testDelete()
    {
        $client = new Client();
        $mock = new MockPlugin();
        $mock->addResponse(new Response(200, array('Foo' => 'bar'), 'body'));
        $client->addSubscriber($mock);

        $expected = new HttpResponse("HTTP/1.1 200 OK\r\nFoo: bar\r\n\r\nbody");
        $this->assertEquals($expected, $client->delete('http://www.example.com/'));
    }

    public function testGetCatchesHttpException()
    {
        $client = new Client();
        $mock = new MockPlugin();
        $mock->addResponse(new Response(400, array('Foo' => 'bar'), 'body'));
        $client->addSubscriber($mock);

        $expected = new HttpResponse("HTTP/1.1 400 Bad Request\r\nFoo: bar\r\n\r\nbody");
        $this->assertEquals($expected, $client->get('http://www.example.com/'));
    }

    public function testPostCatchesHttpException()
    {
        $client = new Client();
        $mock = new MockPlugin();
        $mock->addResponse(new Response(400, array('Foo' => 'bar'), 'body'));
        $client->addSubscriber($mock);

        $expected = new HttpResponse("HTTP/1.1 400 Bad Request\r\nFoo: bar\r\n\r\nbody");
        $this->assertEquals($expected, $client->post('http://www.example.com/'));
    }
}
