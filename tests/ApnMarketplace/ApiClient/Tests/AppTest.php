<?php

namespace ApnMarketplace\ApiClient\Tests\App;

use ApnMarketplace\ApiClient\App;
use ApnMarketplace\ApiClient\HttpResponse;
use ApnMarketplace\ApiClient\StreamResponse;

class AppTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStream()
    {
        $stream = $this->getMock('\Guzzle\Stream\StreamInterface');
        $stream->expects($this->any())->method('getMetaData')->will($this->returnValue(array('HTTP/1.1 200 OK', 'Content-Disposition: Attachment; filename="foo.txt";')));
        $client = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Client');
        $client->expects($this->any())->method('getStream')->will($this->returnValue(new StreamResponse($stream)));
        $app = new App($client);

        $response = $app->getStream('url');
        $this->assertInstanceOf('ApnMarketplace\ApiClient\StreamResponse', $response);
        $this->assertEquals('foo.txt', $response->getFilename());
    }

    public function testGet()
    {
        $response1 = <<<EOT
HTTP/1.1 200 OK\r
Header: foo\r
\r
{
    "data": {
        "nested": {
            "target": "foo"
        },
        "links": [
            {
                "rel": "link",
                "href": "xxx"
            }
        ]
    }
}
EOT;
        $response2 = <<<EOT
HTTP/1.1 200 OK\r
Header: foo\r
\r
{
    "data": {
        "link": "link"
    }
}
EOT;

        $client = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Client');
        $client->expects($this->any())->method('get')->will($this->onConsecutiveCalls(new HttpResponse($response1), new HttpResponse($response1), new HttpResponse($response2)));
        $app = new App($client);

        $this->assertEquals('foo', $app->get('nested.target'));
        $link = new \stdClass();
        $link->link = 'link';
        $this->assertEquals($link, $app->get('link'));
    }

    public function testDelete()
    {
        $response = <<<EOT
HTTP/1.1 200 OK\r
Header: foo\r
\r
{
    "data": {
        "foo": "foo"
    }
}
EOT;

        $client = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Client');
        $client->expects($this->any())->method('delete')->will($this->returnValue(new HttpResponse($response)));
        $app = new App($client);

        $foo = new \stdClass();
        $foo->foo = 'foo';
        $this->assertEquals($foo, $app->delete('http://example.com'));
    }

    /**
     * @expectedException ApnMarketplace\ApiClient\Exception\ResourceNotFoundException
     */
    public function testGetThrowsException()
    {
        $response = <<<EOT
HTTP/1.1 200 OK\r
Header: foo\r
\r
{
    "data": {
        "links": []
    }
}
EOT;
        $client = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Client');
        $client->expects($this->any())->method('get')->will($this->returnValue(new HttpResponse($response)));
        $app = new App($client);

        $this->assertEquals('foo', $app->get('NOTHING'));
    }

    public function testPost()
    {
        $response = <<<EOT
HTTP/1.1 200 OK\r
Header: foo\r
\r
{
    "data": {
        "foo": "foo"
    }
}
EOT;

        $client = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Client');
        $client->expects($this->any())->method('post')->will($this->returnValue(new HttpResponse($response)));
        $app = new App($client);

        $foo = new \stdClass();
        $foo->foo = 'foo';
        $this->assertEquals($foo, $app->post('url', 'params'));
    }

    public function testPostFollowsLocation()
    {
        $response1 = <<<EOT
HTTP/1.1 200 OK\r
Location: foo\r
\r
{
    "data": {
        "foo": "foo"
    }
}
EOT;
        $response2 = <<<EOT
HTTP/1.1 200 OK\r
Header: foo\r
\r
{
    "data": {
        "followed": "followed"
    }
}
EOT;

        $client = $this->getMock('\ApnMarketplace\ApiClient\Client\Guzzle\Client');
        $client->expects($this->any())->method('post')->will($this->onConsecutiveCalls(new HttpResponse($response1)));
        $client->expects($this->any())->method('get')->will($this->onConsecutiveCalls(new HttpResponse($response2)));
        $app = new App($client);

        $foo = new \stdClass();
        $foo->followed = 'followed';
        $this->assertEquals($foo, $app->post('url', 'params'));
    }
}
