<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 LinguaLeo
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

class HotCache extends CacheProvider
{
    protected $cache = [];

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $ttl = 0)
    {
        $this->cache[$key] = $data;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        $data = $this->get($key);
        $modifier($data);
        $this->set($key, $data);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        if (array_key_exists($key, $this->cache)) {
            $modifier($this->cache[$key]);
            return $this->cache[$key];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function mdelete(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->cache[$key]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Value of incrementation must be greater that nil.');
        }

        if (isset($this->cache[$key])) {
            $this->cache[$key] += $value;
        } else {
            $this->cache[$key] = $value;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function mget(array $keys)
    {
        $result = array();

        foreach ($keys as $key) {
            if (isset($this->cache[$key])) {
                $result[$key] = $this->cache[$key];
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function mset(array $data, $ttl = 0)
    {
        $count = 0;
        foreach ($data as $key => $value) {
            if ($this->set($key, $value, $ttl)) {
                $count++;
            }
        }
        return $count;
    }


    /**
     * {@inheritdoc}
     */
    public function add($key, $data, $ttl = 0)
    {
        if (isset($this->cache[$key])) {
            return false;
        }

        $this->cache[$key] = $data;
        return true;
    }
}
