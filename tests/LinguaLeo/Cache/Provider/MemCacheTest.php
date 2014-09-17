<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 15.09.14
 * Time: 16:10
 */

namespace LinguaLeo\Cache\Provider;

use LinguaLeo\Cache\Provider\MemcachedProvider;

class MemCacheTest extends BaseCacheTest
{

    const HOST = '192.168.57.94'; // looks at Vagrantfile in the project root
    const PORT = 11211;

    public function getCache()
    {
        $memcached = new \Memcached();
        $memcached->addServer(self::HOST, self::PORT);
        $cache = new MemCache($memcached);
        $cache->flush();
        return $cache;
    }

    public function testErrorMultiSet()
    {
        $memcached = $this->getMock(
            \Memcached::class,
            ['setMulti']
        );
        $memcached->expects($this->once())->method('setMulti')->will($this->returnValue(false));
        $cacheProvider = new MemCache($memcached);
        $this->assertEquals(0, $cacheProvider->mset([]));
    }

    /**
     * @expectedException \LinguaLeo\Cache\Exception\AtomicViolationException
     */
    public function testErrorIncrement()
    {
        $memcached = $this->getMock(
            \Memcached::class,
            ['add']
        );
        $memcached->expects($this->once())->method('add')->will($this->returnValue(false));
        $cacheProvider = new MemCache($memcached);
        $this->assertFalse($cacheProvider->increment('test'));
    }
}
