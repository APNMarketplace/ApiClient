<?php

namespace ApnMarketplace\ApiClient;

class HttpResponse
{
    private $protocolVersion;
    private $statusCode;
    private $statusText;
    private $headers;
    private $body;

    /**
     * @param string $response An HTTP response
     */
    public function __construct($response)
    {
        list($head, $body) = explode("\r\n\r\n", $response);
        list($status, $headers) = explode("\r\n", $head, 2);
        list($version, $code, $text) = explode(' ', $status, 3);
        $this->protocolVersion = $version;
        $this->statusCode = $code;
        $this->statusText = $text;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Get the message body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the message headers
     *
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the protocol version, eg HTTP/1.1
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Get the status code, eg 200
     *
     * @return int
     */
    public function getStatusCode()
    {
        return (int)$this->statusCode;
    }

    /**
     * Get the status message, eg Not Found
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * Get the status code and status text, eg 200 OK
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->statusCode.' '.$this->statusText;
    }

    /**
     * Whether the response was a client error (4xx)
     *
     * @return bool
     */
    public function isClientError()
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    /**
     * Whether the response was a server error (5xx)
     * @return bool
     */
    public function isServerError()
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    /**
     * Whether the response was an error (4xx or 5xx)
     *
     * @return bool
     */
    public function isError()
    {
        return $this->isClientError() || $this->isServerError();
    }

    /**
     * Does the response have a Location: header?
     *
     * @return bool
     */
    public function hasLocation()
    {
        return stripos($this->getHeaders(), 'location:') !== false;
    }

    /**
     * Get the URL in the Location: header
     *
     * @return string
     */
    public function getLocation()
    {
        foreach (explode("\r\n", $this->getHeaders()) as $header) {
            if (stripos(trim($header), 'location:') === 0) {
                return trim(str_ireplace('location:', '', $header), " ;\r\n");
            }
        }
    }
}
