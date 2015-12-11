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

class HotCacheTest extends BaseCacheTest
{
    const DB_INDEX = 15;

    public function getCache()
    {
        return new HotCache();
    }

    public function testErrorMultiSet()
    {
        $this->assertEquals(0, $this->cache->mset([], 0));
    }

    public function testSetObject()
    {
        $this->cache->set('test', [1, 2]);
        $this->assertEquals([1, 2], $this->cache->get('test'));
    }

    public function testMultiGet()
    {
        $this->cache->mset([
            'cache/25' => 'Object25',
            'cache/1' => 'Object1',
        ]);

        $this->assertEquals(
            [
                'cache/25' => 'Object25',
                'cache/1' => 'Object1',

            ],
            $this->cache->mget([25 => 'cache/25', 10 => 'cache/10', 1 => 'cache/1'])
        );
    }

    public function testCreateAtomicViolation()
    {
        // HotCache doesn't throw AtomicViolationException
    }

    public function testUpdateAtomicViolation()
    {
        // HotCache doesn't throw AtomicViolationException
    }
}
