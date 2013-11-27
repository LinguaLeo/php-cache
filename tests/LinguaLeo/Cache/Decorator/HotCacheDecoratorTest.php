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
        $cache = new HotCacheDecorator($this->wrappedCache, false);
        $cache->flush();
        return $cache;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCliException()
    {
        new HotCacheDecorator($this->wrappedCache);
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