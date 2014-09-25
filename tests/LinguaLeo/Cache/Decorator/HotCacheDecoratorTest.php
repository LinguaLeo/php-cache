<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 LinguaLeo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
            'test2' => 'data2'
        ];
        $this->assertEquals($data, $this->cache->mget(['test1', 'test2']));
    }
}
