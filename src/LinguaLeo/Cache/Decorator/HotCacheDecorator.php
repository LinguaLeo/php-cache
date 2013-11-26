<?php

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
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get data by key.
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $this->gc();
        if (isset($this->hot[$key])) {
            return $this->hot[$key];
        }
        return $this->hot[$key] = $this->cache->get($key);
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
        $this->gc();
        if ($result = $this->cache->set($key, $data, $ttl)) {
            $this->hot[$key] = $data;
        }
        return $result;
    }

    /**
     * Atomically create or replace data by the key using callable modifier.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return bool
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        $this->gc();
        if ($data = $this->cache->create($key, $modifier, $ttl)) {
            $this->hot[$key] = $data;
        }
        return $data;
    }

    /**
     * Atomically update existing data by the key using callable modifier. If key does not
     * exists not operations will be performed.
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return bool
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        $this->gc();
        if ($data = $this->cache->update($key, $modifier, $ttl)) {
            $this->hot[$key] = $data;
        }
        return $data;
    }

    /**
     * Delete key and associated data.
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $this->gc();
        unset($this->hot[$key]);
        return $this->cache->delete($key);
    }

    /**
     * Atomically increment integer value by the key.
     * @param $key
     * @param int $value
     * @return int new value
     */
    public function increment($key, $value = 1)
    {
        $this->gc();
        $newValue = $this->cache->increment($key, $value);
        $this->hot[$key] = $newValue;
        return $newValue;
    }

    /**
     * Flush all cache data
     * @return bool
     */
    public function flush()
    {
        $this->gc(true);
        return $this->cache->flush();
    }

    /**
     * Garbage collector for CLI applications
     * @param bool $force
     */
    private function gc($force = false)
    {
        if (PHP_SAPI !== 'cli' && $force === false) {
            return;
        }
        if ($force || mt_rand(0, 100) % 20 === 0) {
            $this->hot = [];
        }
    }

}