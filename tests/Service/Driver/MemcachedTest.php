<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Memcached;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;

class MemcachedTest extends TestCase
{
    /**
     * @var Memcached
     */
    protected $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Memcached
     */
    protected $memcached;

    /**
     * @var \DateTime
     */
    protected $time;

    const KEY_PREFIX = 'foo';

    const DATE_KEY = 'foo';

    protected function setUp()
    {
        $this->time = new \DateTime();
        $this->memcached = $this->getMock(\Memcached::class);
        $this->driver = new Memcached($this->memcached, self::KEY_PREFIX);
    }

    public function testGetNull()
    {
        $cache_cb = null;
        $cas_token = null;
        $udf_flags = null;
        $this->memcached
            ->expects($this->once())
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $cache_cb, $cas_token, $udf_flags)
            ->will($this->returnValue(null));

        $this->assertNull($this->driver->get(self::DATE_KEY));
    }

    public function testGet()
    {
        $cache_cb = null;
        $cas_token = null;
        $udf_flags = null;
        $this->memcached
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $cache_cb, $cas_token, $udf_flags)
            ->will($this->returnCallback(function () {
                return clone $this->time;
            }));

        $this->assertNotEquals($this->time, $this->driver->get(self::DATE_KEY)->modify('+1 day'));
        $this->assertEquals($this->time, $this->driver->get(self::DATE_KEY));
    }

    public function testSet()
    {
        $cache_cb = null;
        $cas_token = null;
        $udf_flags = null;
        $this->memcached
            ->expects($this->at(1))
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $cache_cb, $cas_token, $udf_flags)
            ->will($this->returnValue(null));

        $this->memcached
            ->expects($this->once())
            ->method('set')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $this->time, null);

        $this->memcached
            ->expects($this->at(3))
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $cache_cb, $cas_token, $udf_flags)
            ->will($this->returnCallback(function () {
                return clone $this->time;
            }));

        $this->assertTrue($this->driver->set(self::DATE_KEY, $this->time));
        $this->assertTrue($this->driver->set(self::DATE_KEY, $this->time->modify('-1 day')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMaxEmpty()
    {
        $this->driver->getMax([]);
    }

    public function testRemove()
    {
        $this->memcached
            ->expects($this->once())
            ->method('delete')
            ->with(self::KEY_PREFIX.self::DATE_KEY)
            ->will($this->returnValue(true));

        $this->assertTrue($this->driver->remove(self::DATE_KEY));
    }

    public function testGetMax()
    {
        $this->assertEquals($this->time, $this->driver->getMax([$this->time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');

        $cache_cb = null;
        $cas_token = null;
        $udf_flags = null;
        $this->memcached
            ->expects($this->once())
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $cache_cb, $cas_token, $udf_flags)
            ->will($this->returnCallback(function () use ($foo_time) {
                return clone $foo_time;
            }));

        $this->assertEquals($foo_time, $this->driver->getMax([self::DATE_KEY, $this->time]));
    }

    /**
     * @return Memcached
     */
    protected function getDriver()
    {
        return new Memcached($this->memcached, self::KEY_PREFIX);
    }
}
