<?php

namespace ApnMarketplace\ApiClient;

use ApnMarketplace\ApiClient\Exception\ResourceNotFoundException;
use ApnMarketplace\ApiClient\Exception\InvalidArgumentException;
use ApnMarketplace\ApiClient\Exception\HttpException;
use ApnMarketplace\ApiClient\Client\ClientInterface;

class App
{
    protected $host = 'https://local.api.apnmarketplace.co.nz';
    protected $client;

    /**
     * @param \ApnMarketplace\ApiClient\Client\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Make a POST request
     *
     * @param string $uri The URL to make the request against
     * @param string $params The query string
     * @return \stdClass
     */
    public function post($uri, $params)
    {
        return $this->getResource('POST', $uri, $params);
    }

    /**
     * Make a DELETE request
     *
     * @param string $uri The resource to delete
     * @return \stdClass
     */
    public function delete($uri)
    {
        return $this->getResource('DELETE', $uri);
    }

    /**
     * Make a GET request and return the resource
     *
     * @param string $resource A URL or period separated path to a resource
     * @return \stdClass
     * @throws ResourceNotFoundException
     */
    public function get($resource = null)
    {
        if ($resource === null) {
            $resource = $this->host;
        }

        // given a url, fetch it
        if (strpos($resource, 'https://') === 0) {
            return $this->getResource('GET', $resource);
        }

        // given a path like search.foo.bar
        $res = $this->getResource('GET', $this->host);
        $path = explode('.', $resource);
        while (!empty($path)) {

            $current = array_shift($path);

            // if $current is a property on $res
            if (isset($res->$current)) {
                $res = $res->$current;
                // if max depth return it
                if (empty($path)) {
                    return $res;
                }
                continue;
            }

            // $current is not a propery of $res, look for a link
            $link = $this->getLink($res->links, $current);
            if ($link) {
                $res = $this->getResource('GET', $link->href);
                if (empty($path)) {
                    return $res;
                }
            }
        }
        throw new ResourceNotFoundException('Resource "'.$resource.'" not found');
    }

    /**
     * Make a request and return the data object in the response
     *
     * @param string $method HTTP Method
     * @param string $uri
     * @param string $params Querystring
     * @return \stdClass
     */
    private function getResource($method, $uri, $params = null)
    {
        switch (strtoupper($method)) {
            case 'GET':
                $response = $this->client->get($uri);
                break;
            case 'POST':
                $response = $this->client->post($uri, null, $params);
                // get new resource from location header on create/update
                if ($response->hasLocation()) {
                    $response = $this->client->get($response->getLocation());
                }
                break;
            case 'DELETE':
                $response = $this->client->delete($uri);
                break;
            default:
                throw new InvalidArgumentException('Invalid HTTP method: "'.$method.'"');

        }
        $resource = json_decode($response->getBody());

        if ($response->isError()) {
            $message = $resource === null ? $response->getStatus() : $resource->error->message;
            throw new HttpException($message, $response->getStatusCode());
        }

        return $resource === null ? null : $resource->data;
    }

    /**
     * Get a link from an array of links based on the rel
     *
     * @param array $links
     * @param string $rel
     * @return \stdClass
     */
    private function getLink(array $links, $rel = 'self')
    {
        foreach ($links as $link) {
            if ($link->rel === $rel) {
                return $link;
            }
        }
    }
}