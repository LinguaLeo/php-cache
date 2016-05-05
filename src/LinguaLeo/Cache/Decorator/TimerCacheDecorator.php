<?php
namespace LinguaLeo\Cache\Decorator;

use LinguaLeo\Cache\CacheInterface;

class TimerCacheDecorator implements CacheInterface
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $provider;

    /**
     * @param CacheInterface $cache
     * @param string $provider
     */
    public function __construct(CacheInterface $cache, string $provider)
    {
        $this->cache = $cache;
        $this->provider = $provider;
    }

    /**
     * Wrap each cache interface operation by timer
     *
     * @param string $methodName
     * @param callable $func
     * @return mixed
     */
    private function timeMeasure(string $methodName, callable $func)
    {
        $timer = \sfTimerManager::getTimer('TimerCacheDecorator@' . $methodName, [
            'provider' => $this->provider,
            'operation' => $methodName,
        ]);
        $result = $func();
        $timer->addTime();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key) {
            return $this->cache->get($key);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $ttl = 0)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key, $data, $ttl) {
            return $this->cache->set($key, $data, $ttl);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key, $modifier, $ttl) {
            return $this->cache->create($key, $modifier, $ttl);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key, $modifier, $ttl) {
            return $this->cache->update($key, $modifier, $ttl);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key) {
            return $this->cache->delete($key);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function mdelete(array $keys)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($keys) {
            return $this->cache->mdelete($keys);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key, $value) {
            return $this->cache->increment($key, $value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->timeMeasure(__FUNCTION__, function() {
            return $this->cache->flush();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function mget(array $keys)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($keys) {
            return $this->cache->mget(array_values($keys));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function mset(array $data, $ttl = 0)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($data, $ttl) {
            return $this->cache->mset($data, $ttl);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $data, $ttl = 0)
    {
        return $this->timeMeasure(__FUNCTION__, function() use ($key, $data, $ttl) {
            return $this->cache->add($key, $data, $ttl);
        });
    }
}
