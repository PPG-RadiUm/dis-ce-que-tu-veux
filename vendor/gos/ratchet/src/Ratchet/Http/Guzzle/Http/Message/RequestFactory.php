<?php

namespace Ratchet\Http\Guzzle\Http\Message;

use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\RequestFactory as GuzzleRequestFactory;

class RequestFactory extends GuzzleRequestFactory
{
    protected static $ratchetInstance;

    /**
     * {@inheritdoc}
     */
    public static function getInstance()
    {
        // @codeCoverageIgnoreStart
        if (!static::$ratchetInstance) {
            static::$ratchetInstance = new static();
        }
        // @codeCoverageIgnoreEnd

        return static::$ratchetInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function create($method, $url, $headers = null, $body = '', array $options = array())
    {
        $c = $this->entityEnclosingRequestClass;
        $request = new $c($method, $url, $headers);
        $request->setBody(EntityBody::factory($body));

        return $request;
    }
}
