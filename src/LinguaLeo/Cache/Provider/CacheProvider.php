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

use LinguaLeo\Cache\CacheInterface;

abstract class CacheProvider implements CacheInterface
{
    protected static $prefix = "cache";
    protected static $delimiter = ":";

    /**
     * @param string $prefix
     */
    public static function setPrefix($prefix)
    {
        self::$prefix = $prefix;
    }

    /**
     * @param string $delimiter
     */
    public static function setDelimiter($delimiter)
    {
        self::$delimiter = $delimiter;
    }

    /**
     * Generate cache key.
     * Optionally you can pass a class name as the first parameter and if
     * this class will contain constant named "VERSION" than it will be mixed up
     * with the result key.
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function generateCacheKey()
    {
        $args = func_get_args();
        if (empty($args)) {
            throw new \InvalidArgumentException('At least one argument must be passed to generate cache key.');
        }
        $const = $args[0] . '::VERSION';
        $version = defined($const) ? self::$delimiter . constant($const) : '';
        return self::$prefix . self::$delimiter . implode(self::$delimiter, $args) . $version;
    }

    /**
     * Generate cache keys.
     * The last parameter must be an array
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function generateCacheKeys()
    {
        $args = func_get_args();
        if (empty($args)) {
            throw new \InvalidArgumentException('At least one argument must be passed to generate cache key.');
        }
        $ids = array_pop($args);
        if (!is_array($ids)) {
            throw new \InvalidArgumentException('The last parameter must be an array.');
        }
        $commonKey = call_user_func_array(['self', 'generateCacheKey'], $args);
        $result = [];
        foreach ($ids as $id) {
            $result[] = $commonKey . self::$delimiter . $id;
        }
        return $result;
    }
    /**
    * Generate cache keys.
    * The last parameter must be an array
    * @return array
    * @throws \InvalidArgumentException
    */
    public static function generateCacheKeysArray()
    {
        $args = func_get_args();
        if (empty($args)) {
            throw new \InvalidArgumentException('At least one argument must be passed to generate cache key.');
        }
        $ids = array_pop($args);
        if (!is_array($ids)) {
            throw new \InvalidArgumentException('The last parameter must be an array.');
        }
        $commonKey = call_user_func_array(['self', 'generateCacheKey'], $args);
        $result = [];
        foreach ($ids as $id) {
            $result[] = $commonKey . self::$delimiter . $id;
        }
        return $result;
    }
}
