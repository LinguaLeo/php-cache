<?php

namespace LinguaLeo\Cache\Decorator;

use LinguaLeo\Cache\Provider\RedisCache;
use LinguaLeo\Cache\Provider\RedisCacheTest;

class HotCacheDecoratorTest extends RedisCacheTest
{

    public function getCache()
    {
        $redis = $this->getRedisClient();
        $redis->flushDB();
        return new HotCacheDecorator(new RedisCache($redis));
    }

}