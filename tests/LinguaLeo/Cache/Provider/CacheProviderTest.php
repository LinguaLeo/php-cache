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

class CacheProviderTest extends \PHPUnit_Framework_TestCase
{
    const PREFIX = "cache";
    const DELIMITER = "-";
    const VERSION = 10;

    public function setUp()
    {
        CacheProvider::setPrefix(self::PREFIX);
        CacheProvider::setDelimiter(self::DELIMITER);
    }

    public function testGenerateCacheKey()
    {
        $key = CacheProvider::generateCacheKey('test', 'arg');
        $this->assertEquals(self::PREFIX . self::DELIMITER . 'test' . self::DELIMITER . 'arg', $key);
    }

    public function testGenerateCacheKeyForClass()
    {
        $className = get_class($this);
        $key = CacheProvider::generateCacheKey($className, 'arg');
        $this->assertEquals(
            self::PREFIX . self::DELIMITER . $className . self::DELIMITER . 'arg' . self::DELIMITER . self::VERSION,
            $key
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNegativeCacheKey()
    {
        CacheProvider::generateCacheKey();
    }
}
