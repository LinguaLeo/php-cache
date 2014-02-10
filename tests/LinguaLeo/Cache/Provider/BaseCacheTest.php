<?php

namespace LinguaLeo\Cache\Provider;

use LinguaLeo\Cache\CacheInterface;

abstract class BaseCacheTest extends \PHPUnit_Framework_TestCase
{

    /** @var CacheInterface */
    protected $cache;

    public function setUp()
    {
        $this->cache = $this->getCache();
    }

    /**
     * @return CacheInterface
     */
    abstract protected function getCache();

    public static function testSetAndGetProvider()
    {
        $object = new \stdClass();
        $object->field = 'value';
        $object->booleanField = true;
        $object->arrayField = ['testOne', 'testTwo'];
        return [
            ['test', 'data'],
            ['test', 'newData'],
            ['test', (array)$object, 5],
            ['test', $object, 5],
            ['test', null],
            ['test', true],
            ['test', 10]
        ];
    }
    /**
     * @dataProvider testSetAndGetProvider
     */
    public function testSetAndGet($key, $value, $ttl = 0)
    {
        $this->cache->set($key, $value, $ttl);
        $this->assertEquals($value, $this->cache->get($key));
    }

    public function testMultiSetAndGet()
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];
        $keys = array_keys($data);
        $this->assertTrue($this->cache->mset($data));
        $this->assertEquals($data, $this->cache->mget($keys));
    }

    public function testDelete()
    {
        $this->cache->set('test', 'data');
        $this->assertEquals('data', $this->cache->get('test'));
        $this->cache->delete('test');
        $this->assertFalse($this->cache->get('test'));
    }

    public function testMultiDelete()
    {
        $this->cache->set('test1', 'data');
        $this->cache->set('test2', 'data');
        $result = $this->cache->delete(['test1', 'test2']);
        $this->assertEquals(2, $result);
        $this->assertFalse($this->cache->get('test1'));
        $this->assertFalse($this->cache->get('test2'));
    }

    public function testPositiveIncrement()
    {
        $this->assertEquals(1, $this->cache->increment('test'));
        $this->assertEquals(1, $this->cache->get('test'));
        $this->assertEquals(3, $this->cache->increment('test', 2));
        $this->assertEquals(3, $this->cache->get('test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNegativeIncrement()
    {
        $this->cache->increment('test');
        $this->cache->increment('test', -100);
    }

    public function testCreateNew()
    {
        $modifier = function(&$data) {
            $data = 'value';
        };
        $result = $this->cache->create('test', $modifier);
        $this->assertEquals('value', $result);
        $this->assertEquals('value', $this->cache->get('test'));
    }

    public function testCreateWithReplace()
    {
        $this->cache->set('test', ['value1', 'value2']);
        $modifier = function(&$data) {
            $data[1] = 'value3';
        };
        $result = $this->cache->create('test', $modifier, 10);
        $this->assertEquals(['value1', 'value3'], $result);
        $this->assertEquals(['value1', 'value3'], $this->cache->get('test'));
    }

    /**
     * @expectedException \LinguaLeo\Cache\Exception\AtomicViolationException
     */
    public function testCreateAtomicViolation()
    {
        $this->assertFalse($this->cache->get('test'));
        $modifier = function(&$data) {
            $this->cache->set('test', 'corrupted'); //atomic violation
            $data = 'value';
        };
        $this->cache->create('test', $modifier);
    }

    public function testUpdate()
    {
        $this->cache->set('test', 'data');
        $this->assertEquals('data', $this->cache->get('test'));
        $modifier = function(&$data) {
            $data = 'newData';
        };
        $result = $this->cache->update('test', $modifier, 10);
        $this->assertEquals('newData', $result);
        $this->assertEquals('newData', $this->cache->get('test'));
    }

    public function testUpdateThatNotExists()
    {
        $modifier = function(&$data) {
            $data = 'newData';
        };
        $this->assertFalse($this->cache->update('test', $modifier));
        $this->assertFalse($this->cache->get('test'));
    }

    /**
     * @expectedException \LinguaLeo\Cache\Exception\AtomicViolationException
     */
    public function testUpdateAtomicViolation()
    {
        $this->cache->set('test', 'data');
        $this->assertEquals('data', $this->cache->get('test'));
        $modifier = function(&$data) {
            $this->cache->set('test', 'corrupted'); //atomic violation
            $data = 'newData';
        };
        $this->cache->update('test', $modifier);
    }

    public function testAddPositive()
    {
        $result = $this->cache->add('test', 'test');
        $this->assertTrue($result);
        $this->assertEquals('test', $this->cache->get('test'));
        $result = $this->cache->add('test2', 'test2', 10);
        $this->assertTrue($result);
        $this->assertEquals('test', $this->cache->get('test'));
    }

    public function testAddNegative()
    {
        $result = $this->cache->add('test', 'test');
        $this->assertTrue($result);
        $result = $this->cache->add('test', 'test2');
        $this->assertFalse($result);
        $this->assertEquals('test', $this->cache->get('test'));
        $result = $this->cache->add('test', 'test2', 10);
        $this->assertFalse($result);
        $this->assertEquals('test', $this->cache->get('test'));
    }

    public function testFlush()
    {
        $this->cache->set('test', 'data');
        $this->assertEquals('data', $this->cache->get('test'));
        $this->assertTrue($this->cache->flush());
        $this->assertFalse($this->cache->get('data'));
    }

} 