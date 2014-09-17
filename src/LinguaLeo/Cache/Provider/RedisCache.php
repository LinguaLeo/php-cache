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

use LinguaLeo\Cache\Exception\AtomicViolationException;

class RedisCache extends CacheProvider
{
    /**
     * @var \Redis|\RedisArray
     */
    protected $redis;

    /**
     * @param \Redis|\RedisArray $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
        if ($redis->getOption(\Redis::OPT_SERIALIZER) === \Redis::SERIALIZER_NONE) {
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $ttl = 0)
    {
        if ($ttl > 0) {
            return $this->redis->setex($key, (int)$ttl, $data);
        }
        return $this->redis->set($key, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        $this->redis->watch($key);
        $data = $this->redis->get($key);
        $modifier($data);
        if ($this->setInTransaction($key, $data, (int)$ttl)) {
            return $data;
        }
        throw new AtomicViolationException("Atomic violation occurred when adding key \"{$key}\".");
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        $this->redis->watch($key);
        $data = $this->redis->get($key);
        if ($data === false) {
            $this->redis->unwatch();
            return false;
        } else {
            $modifier($data);
            if ($this->setInTransaction($key, $data, (int)$ttl)) {
                return $data;
            }
        }
        throw new AtomicViolationException("Atomic violation occurred when updating key \"{$key}\".");
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    private function setInTransaction($key, $data, $ttl = 0)
    {
        if ($ttl > 0) {
            $result = $this->redis->multi()
                ->setex($key, (int)$ttl, $data)
                ->exec();
        } else {
            $result = $this->redis->multi()
                ->set($key, $data)
                ->exec();
        }
        return $result !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return (bool)$this->redis->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function mdelete(array $keys)
    {
        $count = 0;
        foreach ($keys as $key) {
            if ($this->delete($key)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->redis->flushDB();
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Value of incrementation must be greater that nil.');
        }
        if ($value === 1) {
            return $this->redis->incr($key);
        }
        return $this->redis->incrBy($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function mget(array $keys)
    {
        return array_combine($keys, $this->redis->mget($keys));
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
        if ($ttl > 0) {
            $uid = uniqid('temp_key', true);
            $result = $this->redis->multi()
                ->setex($uid, (int)$ttl, $data)
                ->renameNx($uid, $key)
                ->delete($uid)
                ->exec();
            return !empty($result[1]);
        }
        return $this->redis->setnx($key, $data);
    }
}
