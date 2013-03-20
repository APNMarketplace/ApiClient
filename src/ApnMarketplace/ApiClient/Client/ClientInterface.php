<?php

namespace ApnMarketplace\ApiClient\Client;

interface ClientInterface
{
    /**
     * Make a GET request and return a HTTP Response
     *
     * @param string $uri
     * @param array $headers Headers in the format: array(array($key, $value), array($key, $value))
     * @param string $body
     *
     * @return \ApnMarketplace\ApiClient\HttpResponse
     */
    public function get($uri = null, $headers = null, $body = null);

    /**
     * Make a POST request and return a HTTP Response
     *
     * @param string $uri
     * @param array $headers Headers in the format: array(array($key, $value), array($key, $value))
     * @param string $postBody
     *
     * @return \ApnMarketplace\ApiClient\HttpResponse
     */
    public function post($uri = null, $headers = null, $postBody = null);

    /**
     * Make a DELETE request and return a HTTP Response
     *
     * @param string $uri
     * @param array $headers Headers in the format: array(array($key, $value), array($key, $value))
     * @param string $body
     *
     * @return \ApnMarketplace\ApiClient\HttpResponse
     */
    public function delete($uri = null, $headers = null, $body = null);
}
