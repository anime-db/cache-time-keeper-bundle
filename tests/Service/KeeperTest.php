<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
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
     * Driver mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver_mock;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->time = new \DateTime();
        $this->driver_mock = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver');
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->time));

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->get('foo'));
    }

    /**
     * Test get empty last update
     */
    public function testGetEmptyLastUpdate()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue(null));
        $this->driver_mock
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, $this->time);

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->get(Keeper::LAST_UPDATE_KEY));
    }

    /**
     * Test get empty
     */
    public function testGetEmpty()
    {
        $this->driver_mock
            ->expects($this->at(0))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $this->driver_mock
            ->expects($this->at(1))
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue($this->time));

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->get('foo'));
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));

        $obj = new Keeper($this->driver_mock);
        $this->assertTrue($obj->set('foo', $this->time));
    }

    /**
     * Test get max
     */
    public function testGetMax()
    {
        $this->driver_mock
            ->expects($this->exactly(2))
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->getMax(['foo']));
        $this->assertEquals($this->time, $obj->getMax(['foo', Keeper::LAST_UPDATE_KEY]));
    }
}