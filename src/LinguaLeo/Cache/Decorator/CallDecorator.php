<?php

namespace LinguaLeo\Cache\Decorator;

use LinguaLeo\Cache\CacheInterface;
use LinguaLeo\Cache\Provider\CacheProvider;
use LinguaLeo\Cache\TTL;

class CallDecorator
{
    protected $client;
    protected $cache;
    protected $ttl;

    public function __construct($client, CacheInterface $cache, $ttl = TTL::SHORT)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->ttl = $ttl;
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
            $this->cache->add($key, $result, $this->ttl);
        }

        return $result;
    }
}
