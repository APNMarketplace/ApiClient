<?php

namespace ApnMarketplace\ApiClient\Client\Guzzle;

use Guzzle\Http\Client as GuzzleClient;
use ApnMarketplace\ApiClient\Client\ClientInterface;
use Guzzle\Http\Exception\HttpException;
use ApnMarketplace\ApiClient\HttpResponse;
use Guzzle\Stream\PhpStreamRequestFactory;
use ApnMarketplace\ApiClient\StreamResponse;

class Client extends GuzzleClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStream($uri = null, $headers = null, $options = array())
    {
        $request = parent::get($uri, $headers, $options);
        $factory = new PhpStreamRequestFactory();

        return new StreamResponse($factory->fromRequest($request));
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri = null, $headers = null, $options = array())
    {
        try {
            $response = parent::get($uri, $headers, $options)->send();
        }
        catch (HttpException $e) {
            // Guzzle throws exceptions for 400/500 responses, we want to return the string instead
            $response = $e->getResponse();
        }

        return new HttpResponse($response->getRawHeaders().$response->getBody(true));
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        try {
            $response = parent::post($uri, $headers, $postBody, $options)->send();
        }
        catch (HttpException $e) {
            // Guzzle throws exceptions for 400/500 responses, we want to return the string instead
            $response = $e->getResponse();
        }

        return new HttpResponse($response->getRawHeaders().$response->getBody(true));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri = null, $headers = null, $body = null, array $options = array())
    {
        try {
            $response = parent::delete($uri, $headers, $body, $options)->send();
        }
        catch (HttpException $e) {
            // Guzzle throws exceptions for 400/500 responses, we want to return the string instead
            $response = $e->getResponse();
        }

        return new HttpResponse($response->getRawHeaders().$response->getBody(true));
    }
}
