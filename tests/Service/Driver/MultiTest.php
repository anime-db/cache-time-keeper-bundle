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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Multi;

/**
 * Test multi driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class MultiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $fast_mock;

    /**
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $slow_mock;

    /**
     * @var \DateTime
     */
    protected $time;

    protected function setUp()
    {
        $this->time = new \DateTime();
        $this->fast_mock = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface');
        $this->slow_mock = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface');
    }

    public function testGetFast()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->time));
        $this->slow_mock
            ->expects($this->never())
            ->method('get');

        $this->assertEquals($this->time, $this->getDriver()->get('foo'));
    }

    public function testGetSlow()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $this->slow_mock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->time));

        $this->assertEquals($this->time, $this->getDriver()->get('foo'));
    }

    public function testSet()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));
        $this->slow_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));

        $this->assertTrue($this->getDriver()->set('foo', $this->time));
    }

    public function testSetFail()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(false));
        $this->slow_mock
            ->expects($this->never())
            ->method('set');

        $this->assertFalse($this->getDriver()->set('foo', $this->time));
    }

    public function testRemove()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));
        $this->slow_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));

        $this->assertTrue($this->getDriver()->remove('foo'));
    }

    /**
     * Test remove the time by fast driver is failed
     */
    public function testRemoveFromFastFail()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(false));
        $this->slow_mock
            ->expects($this->never())
            ->method('remove');

        $this->assertFalse($this->getDriver()->remove('foo'));
    }

    /**
     * Test remove the time by slow driver is failed
     */
    public function testRemoveFromSlowFail()
    {
        $this->fast_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));
        $this->slow_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(false));

        $this->assertFalse($this->getDriver()->remove('foo'));
    }

    /**
     * @return Multi
     */
    protected function getDriver()
    {
        return new Multi($this->fast_mock, $this->slow_mock);
    }
}
