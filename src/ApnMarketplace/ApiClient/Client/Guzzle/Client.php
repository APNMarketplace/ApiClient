<?php

namespace ApnMarketplace\ApiClient\Client\Guzzle;

use Guzzle\Http\Client as GuzzleClient;
use ApnMarketplace\ApiClient\Client\ClientInterface;
use Guzzle\Http\Exception\HttpException;
use ApnMarketplace\ApiClient\HttpResponse;

class Client extends GuzzleClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($uri = null, $headers = null, $body = null)
    {
        try {
            $response = parent::get($uri, $headers, $body)->send();
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
    public function post($uri = null, $headers = null, $postBody = null)
    {
        try {
            $response = parent::post($uri, $headers, $postBody)->send();
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
    public function delete($uri = null, $headers = null, $body = null)
    {
        try {
            $response = parent::delete($uri, $headers, $body)->send();
        }
        catch (HttpException $e) {
            // Guzzle throws exceptions for 400/500 responses, we want to return the string instead
            $response = $e->getResponse();
        }

        return new HttpResponse($response->getRawHeaders().$response->getBody(true));
    }
}
