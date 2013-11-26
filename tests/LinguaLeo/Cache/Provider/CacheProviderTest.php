<?php

namespace LinguaLeo\Cache\Provider;

class CacheProviderTest extends \PHPUnit_Framework_TestCase
{

    const PREFIX = "cache";
    const DELIMITER = "-";
    const VERSION = 10;

    public function setUp()
    {
        CacheProvider::setPrefix(self::PREFIX);
        CacheProvider::setDelimiter(self::DELIMITER);
    }

    public function testGenerateCacheKey()
    {
        $key = CacheProvider::generateCacheKey('test', 'arg');
        $this->assertEquals(self::PREFIX . self::DELIMITER . 'test' . self::DELIMITER . 'arg', $key);
    }

    public function testGenerateCacheKeyForClass()
    {
        $className = get_class($this);
        $key = CacheProvider::generateCacheKey($className, 'arg');
        $this->assertEquals(
            self::PREFIX . self::DELIMITER . $className . self::DELIMITER . 'arg' . self::DELIMITER . self::VERSION,
            $key
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNegativeCacheKey()
    {
        CacheProvider::generateCacheKey();
    }

}