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

use LinguaLeo\Cache\Provider\RedisCacheTest;
use LinguaLeo\Cache\Provider\RedisCache;


class CallDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \LinguaLeo\Cache\CacheInterface
     */
    protected $cache;

    public function getCache()
    {
        $redis = RedisCacheTest::getRedisClient();
        $redis->flushDB();
        return new RedisCache($redis);
    }

    public function setUp()
    {
        $this->cache = $this->getCache();
    }

    private function createDecorator($client)
    {
        return new CallDecorator($client, $this->cache);
    }

    public function testCallMethod()
    {
        $decorator = $this->createDecorator(new DecoratedMock());
        /* @var $decorator DecoratedMock */
        $this->assertSame($decorator->getUnique(), $decorator->getUnique());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCallUndefinedMethod()
     {
         $decorator = $this->createDecorator(new DecoratedMock());
        /* @var $decorator DecoratedMock */
         $decorator->undefinedMethod();
     }

    /**
     * @expectedException \ReflectionException
     * @expectedExceptionMessage Class 1 does not exist
     */
    public function testCallMethodOnANonObject()
    {
        $this->createDecorator(1)->doSomething();
    }

}
