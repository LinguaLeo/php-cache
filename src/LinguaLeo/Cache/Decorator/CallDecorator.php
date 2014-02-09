<?php

namespace LinguaLeo\Cache\Decorator;

use LinguaLeo\Cache\CacheInterface;
use LinguaLeo\Cache\Provider\CacheProvider;

class CallDecorator
{
    protected $client;
    protected $cache;

    public function __construct($client, CacheInterface $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
    }

    public function __call($method, $arguments)
    {
        if (!is_object($this->client)) {
            throw new \RuntimeException('Cannot to call a method from a non object');
        }

        $key = CacheProvider::generateCacheKey(get_class($this->client), $method, implode(':', $arguments));

        $result = $this->cache->get($key);

        if (false === $result) {
            $result = call_user_func_array([$this->client, $method], $arguments);
            $this->cache->add($key, $result);
        }

        return $result;
    }
}
