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

namespace LinguaLeo\Cache;

interface CacheInterface
{
    /**
     * Get data by key.
     *
     * @param string $key
     * @return string
     */
    public function get($key);

    /**
     * Get data by array of keys
     *
     * @param array $keys
     * @return array
     */
    public function mget(array $keys);

    /**
     * Set data by specified key. Existing key will be replaced by the new one.
     *
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function set($key, $data, $ttl = 0);

    /**
     * Set data by array of keys
     *
     * @param array $data
     * @param int $ttl
     * @return int number of deleted keys
     */
    public function mset(array $data, $ttl = 0);

    /**
     * Set data by specified key only if it does not already exists.
     *
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function add($key, $data, $ttl = 0);

    /**
     * Atomically create or replace data by the key using callable modifier.
     *
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
     *
     * @param string $key
     * @param callable $modifier
     * @param int $ttl
     * @throws \LinguaLeo\Cache\Exception\AtomicViolationException
     * @return mixed updated value or false if value does not exists
     */
    public function update($key, callable $modifier, $ttl = 0);

    /**
     * Delete a key.
     *
     * @param string $key
     * @return bool
     */
    public function delete($key);

    /**
     * Delete keys.
     *
     * @param array $keys
     * @return int number of deleted keys
     */
    public function mdelete(array $keys);

    /**
     * Atomically increment integer value by the key.
     *
     * @param $key
     * @param int $value
     * @throws \InvalidArgumentException
     * @return int new value
     */
    public function increment($key, $value = 1);

    /**
     * Flush all cache data
     *
     * @return bool
     */
    public function flush();
}
