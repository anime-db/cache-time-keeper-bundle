<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Memcache;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;

class MemcacheTest extends TestCase
{
    /**
     * @var Memcache
     */
    protected $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Memcache
     */
    protected $memcache;

    /**
     * @var \DateTime
     */
    protected $time;

    const KEY_PREFIX = 'foo';

    const DATE_KEY = 'foo';

    protected function setUp()
    {
        $this->time = new \DateTime();
        $this->memcache = $this->getMock(\Memcache::class);
        $this->driver = new Memcache($this->memcache, self::KEY_PREFIX);
    }

    public function testGetNull()
    {
        $this->memcache
            ->expects($this->once())
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY)
            ->will($this->returnValue(false));

        $this->assertNull($this->driver->get(self::DATE_KEY));
    }

    public function testGet()
    {
        $this->memcache
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY)
            ->will($this->returnValue($this->time->getTimestamp()));

        $this->assertNotEquals($this->time, $this->driver->get(self::DATE_KEY)->modify('+1 day'));
        $this->assertEquals($this->time, $this->driver->get(self::DATE_KEY));
    }

    public function testSet()
    {
        $this->memcache
            ->expects($this->at(0))
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY)
            ->will($this->returnValue(null));

        $this->memcache
            ->expects($this->once())
            ->method('set')
            ->with(self::KEY_PREFIX.self::DATE_KEY, $this->time->getTimestamp())
            ->will($this->returnValue(true));

        $this->memcache
            ->expects($this->at(2))
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY)
            ->will($this->returnValue($this->time->getTimestamp()));

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
        $this->memcache
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

        $this->memcache
            ->expects($this->once())
            ->method('get')
            ->with(self::KEY_PREFIX.self::DATE_KEY)
            ->will($this->returnValue($foo_time->getTimestamp()));

        $this->assertEquals($foo_time, $this->driver->getMax([self::DATE_KEY, $this->time]));
    }

    /**
     * @return Memcache
     */
    protected function getDriver()
    {
        return new Memcache($this->memcache, self::KEY_PREFIX);
    }
}
