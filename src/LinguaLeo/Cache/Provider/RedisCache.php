<?php

namespace LinguaLeo\Cache\Provider;

use LinguaLeo\Cache\Exception\AtomicViolationException;

class RedisCache extends CacheProvider
{

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @param \Redis $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
        $this->redis->setOption(\Redis::OPT_SERIALIZER, $this->getSerializerValue());
    }

    /**
     * Get data by key.
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * Set data by specified key. Existing key will be replaced by the new one.
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function set($key, $data, $ttl = 0)
    {
        if ($ttl > 0) {
            return $this->redis->setex($key, (int)$ttl, $data);
        }
        return $this->redis->set($key, $data);
    }

    /**
     * Atomically create or replace data by the key using callable modifier.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return mixed added value
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
     * Atomically update existing data by the key using callable modifier. If key does not
     * exists not operations will be performed.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return mixed updated value or false if value does not exists
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
     * Delete key(-s) and associated data.
     * @param string|array $key
     * @return int number of deleted keys
     */
    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * Flush all cache data
     * @return bool
     */
    public function flush()
    {
        return $this->redis->flushDB();
    }

    /**
     * Atomically increment integer value by the key.
     * @param $key
     * @param int $value
     * @throws \InvalidArgumentException
     * @return int
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
     * Returns the serializer constant to use. If Redis is compiled with
     * igbinary support, that is used. Otherwise the default PHP serializer is
     * used.
     * @return integer One of the Redis::SERIALIZER_* constants
     */
    protected function getSerializerValue()
    {
        return defined('\Redis::SERIALIZER_IGBINARY') ? \Redis::SERIALIZER_IGBINARY : \Redis::SERIALIZER_PHP;
    }

    /**
     * Get data by array of keys
     * @param array $keys
     * @return array
     */
    public function mget(array $keys)
    {
        return array_combine($keys, $this->redis->mget($keys));
    }

    /**
     * Set data by array of keys
     * @param array $data
     * @return bool
     */
    public function mset(array $data)
    {
        return $this->redis->mset($data);
    }

    /**
     * Set data by specified key only if it does not already exists.
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
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