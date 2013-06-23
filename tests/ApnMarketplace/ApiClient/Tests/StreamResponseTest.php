<?php

namespace ApnMarketplace\ApiClient\Tests;

use ApnMarketplace\ApiClient\StreamResponse;

class HttpResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \ApnMarketplace\ApiClient\Exception\HttpException
     */
    public function testException()
    {
        $stream = $this->getMock('\Guzzle\Stream\StreamInterface');
        $stream->expects($this->any())->method('getMetaData')->will($this->returnValue(array('HTTP/1.1 400 Fail')));

        $response = new StreamResponse($stream);
    }

    public function testGetFilename()
    {
        $stream = $this->getMock('\Guzzle\Stream\StreamInterface');
        $stream->expects($this->any())->method('getMetaData')->will($this->returnValue(array('HTTP 200 OK', 'Content-Disposition: Attachment; filename="foo.txt";')));

        $response = new StreamResponse($stream);
        $this->assertEquals('foo.txt', $response->getFilename());
    }

    public function testGetStream()
    {
        $stream = $this->getMock('\Guzzle\Stream\StreamInterface');
        $stream->expects($this->any())->method('getMetaData')->will($this->returnValue(array('HTTP 200 OK', 'Content-Disposition: Attachment; filename="foo.txt";')));
        $stream->expects($this->any())->method('getStream')->will($this->returnValue('resource'));

        $response = new StreamResponse($stream);
        $this->assertEquals('resource', $response->getStream());
    }
}
