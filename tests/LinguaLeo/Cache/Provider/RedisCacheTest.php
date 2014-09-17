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

namespace LinguaLeo\Cache\Provider;

class RedisCacheTest extends BaseCacheTest
{
    const HOST = '192.168.57.94'; // looks at Vagrantfile in the project root
    const PORT = 6379;
    const DB_INDEX = 15;

    public function getCache()
    {
        $redis = self::getRedisClient();
        $redis->flushDB();
        return new RedisCache($redis);
    }

    public static function getRedisClient()
    {
        $redis = new \Redis();
        $redis->connect(self::HOST, self::PORT);
        $redis->select(self::DB_INDEX);
        return $redis;
    }

    public function testErrorMultiSet()
    {
        $wrappedRedis = $this->getMock(
            \Redis::class,
            ['mset', 'getOption']
        );
        $wrappedRedis->expects($this->once())->method('mset')->will($this->returnValue(false));
        $wrappedRedis->expects($this->once())->method('getOption')->will($this->returnValue(false));
        $redisProvider = new RedisCache($wrappedRedis);
        $this->assertEquals(0, $redisProvider->mset([], 0));
    }
}
