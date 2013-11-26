<?php

namespace LinguaLeo\Cache\Provider;

use LinguaLeo\Cache\CacheInterface;

class ApcCacheTest extends BaseCacheTest
{

    /**
     * @return CacheInterface
     */
    protected function getCache()
    {
        $apc = new ApcCache();
        $apc->flush();
        return $apc;
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCreateNew()
    {
        parent::testCreateNew();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCreateWithReplace()
    {
        parent::testCreateWithReplace();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCreateAtomicViolation()
    {
        parent::testCreateAtomicViolation();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testUpdate()
    {
        parent::testUpdate();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testUpdateThatNotExists()
    {
        parent::testUpdateThatNotExists();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testUpdateAtomicViolation()
    {
        parent::testUpdateAtomicViolation();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testMultiSetAndGet()
    {
        parent::testMultiSetAndGet();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testMultiGet()
    {
        $this->cache->mget(['test']);
    }

}