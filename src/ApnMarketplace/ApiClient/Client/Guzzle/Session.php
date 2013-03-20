<?php

namespace ApnMarketplace\ApiClient\Client\Guzzle;

class Session
{
    /**
     * Start a session if one has not already been started
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Get a session value
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Set a session value
     *
     * @param string $key
     * @param mixed $val
     */
    public function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }
}
