<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;

/**
 * Test keeper
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class KeeperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper::get
     */
    public function testGet()
    {
        $time = new \DateTime();
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($time));

        $obj = new Keeper($driver_mock);
        $this->assertEquals($time, $obj->get('foo'));
    }

    /**
     * Test get empty last update
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper::get
     */
    public function testGetEmptyLastUpdate()
    {
        $time = new \DateTime();
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->once())
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue(null));
        $driver_mock
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, $time);

        $obj = new Keeper($driver_mock);
        $this->assertEquals($time, $obj->get(Keeper::LAST_UPDATE_KEY));
    }

    /**
     * Test get empty
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper::get
     */
    public function testGetEmpty()
    {
        $time = new \DateTime();
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->at(0))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $driver_mock
            ->expects($this->at(1))
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue($time));

        $obj = new Keeper($driver_mock);
        $this->assertEquals($time, $obj->get('foo'));
    }

    /**
     * Test set
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper::set
     */
    public function testSet()
    {
        $time = new \DateTime();
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $time)
            ->will($this->returnValue(true));

        $obj = new Keeper($driver_mock);
        $this->assertTrue($obj->set('foo', $time));
    }

    /**
     * Test get max
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper::getMax
     */
    public function testGetMax()
    {
        $time = new \DateTime();
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->exactly(2))
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($time));

        $obj = new Keeper($driver_mock);
        $this->assertEquals($time, $obj->getMax(['foo']));
        $this->assertEquals($time, $obj->getMax(['foo', Keeper::LAST_UPDATE_KEY]));
    }
}