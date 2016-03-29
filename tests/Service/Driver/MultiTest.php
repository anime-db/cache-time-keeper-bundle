<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Multi;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface;

/**
 * Test multi driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class MultiTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DriverInterface
     */
    protected $fast_driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DriverInterface
     */
    protected $slow_driver;

    /**
     * @var Multi
     */
    protected $driver;

    /**
     * @var \DateTime
     */
    protected $time;

    protected function setUp()
    {
        $this->fast_driver = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface');
        $this->slow_driver = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface');

        $this->time = new \DateTime();
        $this->driver = new Multi($this->fast_driver, $this->slow_driver);
    }

    public function testGetFast()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->time));
        $this->slow_driver
            ->expects($this->never())
            ->method('get');

        $this->assertEquals($this->time, $this->driver->get('foo'));
    }

    public function testGetSlow()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $this->slow_driver
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->time));

        $this->assertEquals($this->time, $this->driver->get('foo'));
    }

    public function testSet()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));
        $this->slow_driver
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));

        $this->assertTrue($this->driver->set('foo', $this->time));
    }

    public function testSetFail()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(false));
        $this->slow_driver
            ->expects($this->never())
            ->method('set');

        $this->assertFalse($this->driver->set('foo', $this->time));
    }

    public function testRemove()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));
        $this->slow_driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));

        $this->assertTrue($this->driver->remove('foo'));
    }

    /**
     * Test remove the time by fast driver is failed
     */
    public function testRemoveFromFastFail()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(false));
        $this->slow_driver
            ->expects($this->never())
            ->method('remove');

        $this->assertFalse($this->driver->remove('foo'));
    }

    /**
     * Test remove the time by slow driver is failed
     */
    public function testRemoveFromSlowFail()
    {
        $this->fast_driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));
        $this->slow_driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(false));

        $this->assertFalse($this->driver->remove('foo'));
    }
}
