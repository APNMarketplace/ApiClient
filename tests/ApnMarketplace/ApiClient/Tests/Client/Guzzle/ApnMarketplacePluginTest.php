<?php

namespace ApnMarketplace\ApiClient\Tests\Client\Guzzle;

use ApnMarketplace\ApiClient\Client\Guzzle\ApnMarketplacePlugin;
use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestFactory;
use Guzzle\Http\Message\Response;

class ApnMarketplacePluginTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscribesToEvents()
    {
        $events = ApnMarketplacePlugin::getSubscribedEvents();
        $this->assertArrayHasKey('request.before_send', $events);
        $this->assertArrayHasKey('request.complete', $events);
    }

    public function testBeforeSend()
    {
        $client = new Client('http://example.com');
        $session = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Session');
        $session->expects($this->any())->method('get')->will($this->returnValue('TOKEN'));
        $plugin = new ApnMarketplacePlugin('id', 'secret', 'datetime', $session);
        $request = $client->get('/');
        $request->getEventDispatcher()->addSubscriber($plugin);
        $request->send();

        $this->assertEquals('Basic aWQ6MDljNTA4OTYxOTZmZjlkODBmYzQ1NTlmMmU2NjZjZDlhNmFmZmU0ODdiNGFkOGIzYmUxOWViYzQ4ZDllNjUyOQ==', $request->getHeader('Authorization'));
        $this->assertEquals('TOKEN', $request->getHeader('x-api-user-session', true));
        $this->assertEquals('datetime', $request->getHeader('accept-datetime', true));
        $this->assertEquals('header=x-api-user-session', $request->getParams()->get('cache.key_filter'));
    }

    public function testComplete()
    {
        $session = new Session();
        $plugin = new ApnMarketplacePlugin('id', 'secret', 'datetime', $session);
        $request = RequestFactory::getInstance()->create('GET', 'http://example.com/');
        $request->getEventDispatcher()->addSubscriber($plugin);

        // test cached doesn't set token
        $request->dispatch('request.complete', array(
            'response' => new Response(200, array('x-api-user-session' => 'TOKEN', 'age' => '5'))
        ));
        $this->assertEmpty($session->vals['user-token']);

        // test token is set
        $request->dispatch('request.complete', array(
            'response' => new Response(200, array('x-api-user-session' => 'TOKEN'))
        ));
        $this->assertEquals('TOKEN', $session->vals['user-token']);

        // test null doesn't overwrite token
        $request->dispatch('request.complete', array(
            'response' => new Response(200)
        ));
        $this->assertEquals('TOKEN', $session->vals['user-token']);
    }
}

class Session
{
    public $vals;
    public function get($key){ return $this->vals[$key]; }
    public function set($key, $val){ $this->vals[$key] = $val; }
}