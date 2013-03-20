<?php

namespace ApnMarketplace\ApiClient\Tests;

use ApnMarketplace\ApiClient\HttpResponse;

class HttpResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testHttpResponse()
    {
        $string = <<<EOT
HTTP/1.1 203 Non-Authoritative Information\r
Header: foo; bar;\r
\r
Body
EOT;
        $response = new HttpResponse($string);

        $this->assertEquals('Body', $response->getBody());
        $this->assertEquals('Header: foo; bar;', $response->getHeaders());
        $this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
        $this->assertEquals('203 Non-Authoritative Information', $response->getStatus());
        $this->assertEquals(203, $response->getStatusCode());
        $this->assertEquals('Non-Authoritative Information', $response->getStatusText());
    }

    public function testIsClientError()
    {
        $string = <<<EOT
HTTP/1.1 400 Bad Request\r
Header: foo; bar;\r
\r

EOT;
        $response = new HttpResponse($string);

        $this->assertTrue($response->isClientError());
        $this->assertTrue($response->isError());
    }

    public function testIsServerError()
    {
        $string = <<<EOT
HTTP/1.1 500 Internal Server Error\r
Header: foo; bar;\r
\r

EOT;
        $response = new HttpResponse($string);

        $this->assertTrue($response->isServerError());
        $this->assertTrue($response->isError());
    }

    public function testLocation()
    {
        $noLocation = "HTTP/1.1 500 Internal Server Error\r\nHeader: foo;\r\n\r\n";
        $location = "HTTP/1.1 500 Internal Server Error\r\nHeader: foo;\r\nLocation: http://example.com;\r\n\r\n";

        $response = new HttpResponse($noLocation);
        $this->assertFalse($response->hasLocation());
        $this->assertNull($response->getLocation());

        $response = new HttpResponse($location);
        $this->assertTrue($response->hasLocation());
        $this->assertEquals('http://example.com', $response->getLocation());
    }
}
