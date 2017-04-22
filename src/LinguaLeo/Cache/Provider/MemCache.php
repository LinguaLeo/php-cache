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

use \Memcached;

use LinguaLeo\Cache\Exception\AtomicViolationException;

class MemCache extends CacheProvider
{
    /**
     * Memcached instance
     * @var Memcached
     */
    protected $memcached;

    /**
     * @param Memcached $memcached
     */
    public function __construct(Memcached $memcached)
    {
        $this->memcached = $memcached;
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
    public function mset(array $data, $ttl = 0)
    {
        if ($this->memcached->setMulti($data, $ttl)) {
            return count($data);
        }
        return 0;
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
        list($data, $token) = $this->get_with_cas($key);
        if (false === $data) {
            $modifier($data);
            $result = $this->memcached->add($key, $data, $ttl);
        } else {
            $modifier($data);
            $result = $this->memcached->cas($token, $key, $data, $ttl);
        }
        if (!$result) {
            throw new AtomicViolationException(
                sprintf(
                    'Atomic violation occurred "%s" when adding the key "%s" with the token "%g".',
                    $this->memcached->getResultMessage(),
                    $key,
                    $token
                )
            );
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, callable $modifier, $ttl = 0)
    {
        list($data, $token) = $this->get_with_cas($key);
        if (false === $data) {
            return false;
        }
        $modifier($data);
        if ($this->memcached->cas($token, $key, $data, $ttl)) {
            return $data;
        }
        throw new AtomicViolationException(
            sprintf(
                'Atomic violation occurred "%s" when updating the key "%s" with the token "%g".',
                $this->memcached->getResultMessage(),
                $key,
                $token
            )
        );
    }

    protected function get_with_cas($key)
    {
        if (defined('Memcached::GET_EXTENDED')) { // php-memcached >= 3.0.0a1 (?)
            $entry = $this->memcached->get($key, null, Memcached::GET_EXTENDED);
            $value  = $entry['value'] ?? false;
            $cas    = $entry['cas']   ?? null;
        } else {
            $cas = null;
            $value = $this->memcached->get($key, null, $cas);
        }
        return [$value, $cas];
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
        $result = $this->memcached->increment($key, $value);
        if (false === $result) {
            if (false === $this->add($key, $value)) {
                throw new AtomicViolationException(sprintf('In incremention can\'t add key "%s".', $key));
            }
            $result = $value;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->memcached->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function mdelete(array $keys)
    {
        $count = 0;
        /**
         * @array An array with a keys which was deleted, the value if the operation was successful true,
         * otherwise the value that was set in the method deleteMulti
         */
        $results = $this->memcached->deleteMulti($keys);
        foreach ($results as $key => $result) {
            if ($result === true) {
                $count++;
            }
        }
        return $count;
    }

}
