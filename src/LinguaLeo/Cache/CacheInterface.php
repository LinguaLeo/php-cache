<?php

namespace LinguaLeo\Cache;

interface CacheInterface
{

    /**
     * Get data by key.
     * @param string $key
     * @return string
     */
    public function get($key);

    /**
     * Get data by array of keys
     * @param array $keys
     * @return array
     */
    public function mget(array $keys);

    /**
     * Set data by array of keys
     * @param array $data
     * @return bool
     */
    public function mset(array $data);

    /**
     * Set data by specified key. Existing key will be replaced by the new one.
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function set($key, $data, $ttl = 0);

    /**
     * Atomically create or replace data by the key using callable modifier.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return mixed added value
     */
    public function create($key, callable $modifier, $ttl = 0);

    /**
     * Atomically update existing data by the key using callable modifier. If key does not
     * exists not operations will be performed.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return mixed updated value or false if value does not exists
     */
    public function update($key, callable $modifier, $ttl = 0);

    /**
     * Delete key(-s) and associated data.
     * @param string|array $key
     * @return int number of deleted keys
     */
    public function delete($key);

    /**
     * Atomically increment integer value by the key.
     * @param $key
     * @param int $value
     * @throws \InvalidArgumentException
     * @return int new value
     */
    public function increment($key, $value = 1);

    /**
     * Flush all cache data
     * @return bool
     */
    public function flush();

}