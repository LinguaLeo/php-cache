<?php

namespace LinguaLeo\Cache\Decorator;

use LinguaLeo\Cache\Provider\BaseCacheTest;
use LinguaLeo\Cache\Provider\RedisCache;
use LinguaLeo\Cache\Provider\RedisCacheTest;

class HotCacheDecoratorTest extends BaseCacheTest
{

    /**
     * @var RedisCache
     */
    protected $wrappedCache;

    public function getCache()
    {
        $redis = RedisCacheTest::getRedisClient();
        $this->wrappedCache = new RedisCache($redis);
        $cache = new TestHotCacheDecorator($this->wrappedCache);
        $cache->flush();
        return $cache;
    }

    public function testHotMultiGet()
    {
        $this->wrappedCache->set('test1', 'data1');
        $this->cache->set('test2', 'data2');
        $data = [
            'test1' => 'data1',
            'test2' => 'data2',
            'test3' => false,
        ];
        $this->assertEquals($data, $this->cache->mget(['test1', 'test2', 'test3']));
    }

}