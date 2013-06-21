<?php

namespace ApnMarketplace\ApiClient\Client\Guzzle;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApnMarketplacePlugin implements EventSubscriberInterface
{
    private $id;
    private $secret;
    private $cache;
    private $acceptDatetime;

    /**
     *
     * @param string $id Client id
     * @param string $secret Client secret
     * @param CacheInterface
     */
    public function __construct($id, $secret, $acceptDatetime, $session)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->acceptDatetime = $acceptDatetime;
        $this->cache = $session;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            'client.create_request' => array('onClientCreateRequest'),
            'request.complete' => array('onComplete'),
        );
    }

    /**
     * Store the user token returned by the API
     *
     * @param \Guzzle\Common\Event $event
     */
    public function onComplete(Event $event)
    {
        $token = $event['response']->getHeader('x-api-user-session', true);
        $cached = $event['response']->hasHeader('age'); // age header is only present if from cache

        if ($token && !$cached) {
            $this->cache->set('user-token', $token);
        }
    }

    /**
     * Sign the request and set the user token
     *
     * @param \Guzzle\Common\Event $event
     */
    public function onClientCreateRequest(Event $event)
    {
        $request = $event['request'];

        $hash = hash_hmac('sha256', strtoupper($request->getMethod()).' '.$request->getPath(), $this->secret);
        $request->setAuth($this->id, $hash, CURLAUTH_BASIC);
        $request->setHeader('x-api-user-session', $this->cache->get('user-token'));
        $request->getParams()->set('cache.key_filter', 'header=x-api-user-session');
        $request->setHeader('accept-datetime', $this->acceptDatetime);
    }
}
