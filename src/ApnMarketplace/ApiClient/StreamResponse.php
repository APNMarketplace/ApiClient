<?php

namespace ApnMarketplace\ApiClient;

use Guzzle\Stream\StreamInterface;
use ApnMarketplace\ApiClient\Exception\HttpException;

class StreamResponse
{
    private $stream;
    private $filename;

    /**
     * @param \Guzzle\Stream\StreamInterface $stream
     * @throws HttpException
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;

        $headers = $stream->getMetaData('wrapper_data');
        foreach ($headers as $header) {
            if (strpos(strtolower(trim($header)), 'http') === 0) {
                $status = explode(' ', $header, 3);
                if ((int) $status[1] !== 200) {
                    throw new HttpException($header);
                }
            }
            if (strpos(strtolower(trim($header)), 'content-disposition') === 0) {
                $this->filename = preg_match('/filename="(.*)"/', $header, $matches) ? $matches[1] : '';
            }
        }
    }

    /**
     * Get the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the underlying stream
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream->getStream();
    }
}
