<?php

namespace LinguaLeo\Cache\Provider;

class ApcCache extends CacheProvider
{

    /**
     * Get data by key.
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return apc_fetch($key);
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
        return (bool) apc_store($key, $data, (int)$ttl);
    }

    /**
     * Atomically create or replace data by the key using callable modifier.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \BadMethodCallException
     * @return mixed added value
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        throw new \BadMethodCallException('Atomic CAS for mixed vars is not provided by APC.');
    }

    /**
     * Atomically update existing data by the key using callable modifier. If key does not
     * exists not operations will be performed.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \BadMethodCallException
     * @return mixed updated value or false if value does not exists
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        throw new \BadMethodCallException('Atomic CAS for mixed vars is not provided by APC.');
    }

    /**
     * Delete key and associated data.
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return apc_delete($key);
    }

    /**
     * Atomically increment integer value by the key.
     * @param $key
     * @param int $value
     * @throws \InvalidArgumentException
     * @return int new value
     */
    public function increment($key, $value = 1)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Value of incrementation must be greater that nil.');
        }
        apc_add($key, 0);
        return apc_inc($key, $value);
    }

    /**
     * Flush all cache data
     * @return bool
     */
    public function flush()
    {
        return apc_clear_cache() && apc_clear_cache('user');
    }

}