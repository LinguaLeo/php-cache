<?php

namespace LinguaLeo\Cache\Provider;

class RedisCacheTest extends BaseCacheTest
{

    const HOST = '127.0.0.1';
    const PORT = 6379;
    const DB_INDEX = 15;

    public function getCache()
    {
        $redis = self::getRedisClient();
        $redis->flushDB();
        return new RedisCache($redis);
    }

    public static function getRedisClient()
    {
        $redis = new \Redis();
        $redis->connect(self::HOST, self::PORT);
        $redis->select(self::DB_INDEX);
        return $redis;
    }

} 