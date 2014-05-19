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

use LinguaLeo\Cache\CacheInterface;

class HotCacheDecorator implements CacheInterface
{
    /**
     * @var \LinguaLeo\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $hot = [];

    /**
     * @param CacheInterface $cache
     * @param bool $isCliRestricted
     * @throws \RuntimeException
     */
    public function __construct(CacheInterface $cache, $isCliRestricted = true)
    {
        $this->cache = $cache;
        if (PHP_SAPI === 'cli' && $isCliRestricted) {
            throw new \RuntimeException('Hot cache is not allowed to use in CLI mode.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (isset($this->hot[$key])) {
            return $this->hot[$key];
        }
        return $this->hot[$key] = $this->cache->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $ttl = 0)
    {
        $result = $this->cache->set($key, $data, $ttl);
        if ($result) {
            $this->hot[$key] = $data;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        $data = $this->cache->create($key, $modifier, $ttl);
        if ($data) {
            $this->hot[$key] = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        $data = $this->cache->update($key, $modifier, $ttl);
        if ($data) {
            $this->hot[$key] = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $key = (array)$key;
        foreach ($key as $k) {
            unset($this->hot[$k]);
        }
        return $this->cache->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        $newValue = $this->cache->increment($key, $value);
        $this->hot[$key] = $newValue;
        return $newValue;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->hot = [];
        return $this->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function mget(array $keys)
    {
        $result = [];
        foreach ($keys as $index => $key) {
            if (!isset($this->hot[$key])) {
                continue;
            }
            $result[$key] = $this->hot[$key];
            unset($keys[$index]);
        }
        if (count($keys) > 0) {
            $cacheResult = $this->cache->mget($keys);
            foreach ($cacheResult as $key => $value) {
                $result[$key] = $this->hot[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function mset(array $data)
    {
        $result = $this->cache->mset($data);
        if ($result) {
            foreach ($data as $key => $value) {
                $this->hot[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $data, $ttl = 0)
    {
        $result = $this->cache->add($key, $data, $ttl);
        if ($result) {
            $this->hot[$key] = $data;
        }
        return $result;
    }
}
