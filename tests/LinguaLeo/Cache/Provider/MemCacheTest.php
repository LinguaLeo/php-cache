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

use LinguaLeo\Cache\Provider\MemcachedProvider;

class MemCacheTest extends BaseCacheTest
{

    const HOST = '192.168.57.94'; // looks at Vagrantfile in the project root
    const PORT = 11211;

    public function getCache()
    {
        $memcached = new \Memcached();
        $memcached->addServer(self::HOST, self::PORT);
        $cache = new MemCache($memcached);
        $cache->flush();
        return $cache;
    }

    public function testErrorMultiSet()
    {
        $memcached = $this->getMock(
            \Memcached::class,
            ['setMulti']
        );
        $memcached->expects($this->once())->method('setMulti')->will($this->returnValue(false));
        $cacheProvider = new MemCache($memcached);
        $this->assertEquals(0, $cacheProvider->mset([]));
    }

    /**
     * @expectedException \LinguaLeo\Cache\Exception\AtomicViolationException
     */
    public function testErrorIncrement()
    {
        $memcached = $this->getMock(
            \Memcached::class,
            ['add']
        );
        $memcached->expects($this->once())->method('add')->will($this->returnValue(false));
        $cacheProvider = new MemCache($memcached);
        $this->assertFalse($cacheProvider->increment('test'));
    }
}
