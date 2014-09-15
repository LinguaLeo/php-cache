<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 12.09.14
 * Time: 14:41
 */

namespace LinguaLeo\Cache\Provider;

use \Memcached;

class MemcachedProvider extends CacheProvider{
    /**
     * Memcached instance
     * @var \Memcached
     */
    protected $memcached;

    /**
     * @param Memcached $memcached
     * @param $prefixKey
     * @param array $serversNodes
     */
    public function __construct(\Memcached $memcached, $prefixKey,array $serversNodes)
    {
        $this->memcached = $memcached;
        $options = [
            Memcached::OPT_PREFIX_KEY => $prefixKey,
            Memcached::OPT_HASH => Memcached::HASH_MD5,
            Memcached::OPT_DISTRIBUTION => Memcached::DISTRIBUTION_MODULA,
            Memcached::OPT_TCP_NODELAY => 1
        ];
        $this->memcached->setOptions($options);
        $this->memcached->addServers($serversNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->memcached->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function mget(array $keys)
    {
        return $this->memcached->getMulti($keys);
    }

    /**
    * {@inheritdoc}
    */
    public function mset(array $data)
    {
        return $this->memcached->setMulti($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $ttl = 0)
    {
        return $this->memcached->set($key, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $data, $ttl = 0)
    {
        return $this->memcached->add($key, $data, $ttl);
    }


    /**
     * {@inheritdoc}
     */
    public function create($key, callable $modifier, $ttl = 0)
    {
        $result = false;
        $token = null;
        $data = $this->memcached->get($key, null, $token);
        if (false === $data) {
            $modifier($data);
            $result = $this->memcached->add($key, $data, $ttl);
        } else {
            $modifier($data);
            $result = $this->memcached->cas($token, $key, $data, $ttl);
        }
        if (!$result)
//        throw new AtomicViolationException("Atomic violation occurred when adding key \"{$key}\".");
            $this->delete($key);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        $token = null;
        $data = $this->memcached->get($key, null, $ttl);
        if (false === $data) {
            return false;
        } elseif (false !== $modifier($data) && $this->memcached->cas($token, $key, $data, $ttl)) {
            return $data;
        } else {
            $this->delete($key);
        }
   //     throw new AtomicViolationException("Atomic violation occurred when updating key \"{$key}\".");
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Value of incrementation must be greater that nil.');
        }
        return $this->memcached->increment($key,$value);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->memcached->flush();
    }

}
