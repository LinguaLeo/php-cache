<?php

namespace LinguaLeo\Cache\Decorator;

use LinguaLeo\Cache\CacheInterface;
use LinguaLeo\Cache\Provider\CacheProvider;
use LinguaLeo\Cache\TTL;

class CallDecorator
{

    /** @var mixed */
    protected $decoratedObject;
    /** @var \LinguaLeo\Cache\CacheInterface */
    protected $cache;
    /** @var int */
    protected $ttl;

    /**
     * @param mixed $decoratedObject
     * @param CacheInterface $cache
     * @param int $ttl
     */
    public function __construct($decoratedObject, CacheInterface $cache, $ttl = TTL::SHORT)
    {
        $this->decoratedObject = $decoratedObject;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \RuntimeException
     */
    public function __call($method, $arguments)
    {
        if (!is_object($this->decoratedObject)) {
            throw new \RuntimeException('Cannot to call a method for a non object.');
        }
        $key = CacheProvider::generateCacheKey(get_class($this->decoratedObject), $method, implode(':', $arguments));
        $result = $this->cache->get($key);
        if ($result === false) {
            $result = call_user_func_array([$this->decoratedObject, $method], $arguments);
            $this->cache->add($key, $result, $this->ttl);
        }
        return $result;
    }

}
