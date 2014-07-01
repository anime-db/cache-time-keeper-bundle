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
     * Time
     *
     * @var \DateTime
     */
    protected $time;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->time = new \DateTime();
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->time));

        $obj = new Keeper($driver_mock);
        $this->assertEquals($this->time, $obj->get('foo'));
    }

    /**
     * Test get empty last update
     */
    public function testGetEmptyLastUpdate()
    {
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
            ->with(Keeper::LAST_UPDATE_KEY, $this->time);

        $obj = new Keeper($driver_mock);
        $this->assertEquals($this->time, $obj->get(Keeper::LAST_UPDATE_KEY));
    }

    /**
     * Test get empty
     */
    public function testGetEmpty()
    {
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
            ->will($this->returnValue($this->time));

        $obj = new Keeper($driver_mock);
        $this->assertEquals($this->time, $obj->get('foo'));
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));

        $obj = new Keeper($driver_mock);
        $this->assertTrue($obj->set('foo', $this->time));
    }

    /**
     * Test get max
     */
    public function testGetMax()
    {
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->exactly(2))
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        $obj = new Keeper($driver_mock);
        $this->assertEquals($this->time, $obj->getMax(['foo']));
        $this->assertEquals($this->time, $obj->getMax(['foo', Keeper::LAST_UPDATE_KEY]));
    }
}