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
     * Get data by key.
     * @param string $key
     * @return string
     */
    public function get($key)
    {
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
        $result = $this->cache->set($key, $data, $ttl);
        if ($result) {
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
        $data = $this->cache->create($key, $modifier, $ttl);
        if ($data) {
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
        $data = $this->cache->update($key, $modifier, $ttl);
        if ($data) {
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
        $key = (array)$key;
        foreach ($key as $k) {
            unset($this->hot[$k]);
        }
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
        $this->hot = [];
        return $this->cache->flush();
    }

    /**
     * Get data by array of keys
     * @param array $keys
     * @return array
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
     * Set data by array of keys
     * @param array $data
     * @return bool
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