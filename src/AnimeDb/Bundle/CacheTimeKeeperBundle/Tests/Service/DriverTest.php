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

/**
 * Test driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get null
     */
    public function testGetNull()
    {
        $this->assertNull($this->getDriver()->get('foo'));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $time = new \DateTime();
        $driver = $this->getDriver();
        $this->assertTrue($driver->set('foo', $time));
        $this->assertEquals($time, $driver->get('foo'));
        $this->assertNotEquals($time, $driver->get('foo')->modify('+1 day'));
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $time = new \DateTime();
        $driver = $this->getDriver();
        $this->assertTrue($driver->set('foo', $time));
        $this->assertTrue($driver->set('foo', $time->modify('-1 day')));
    }

    /**
     * Test sync list times
     */
    public function testSync()
    {
        $time = new \DateTime();
        $first = $this->getDriver();
        $first->set('foo', $time);

        // new object
        $second = $this->getDriver();
        $this->assertEquals($time, $second->get('foo'));
    }

    /**
     * Test get max empty params
     *
     * @expectedException InvalidArgumentException
     */
    public function testGetMaxEmpty()
    {
        $this->getDriver()->getMax([]);
    }

    /**
     * Test get max
     */
    public function testGetMax()
    {
        $time = new \DateTime();
        $driver = $this->getDriver();
        $this->assertEquals($time, $driver->getMax([$time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');
        $driver->set('foo', $foo_time);
        $this->assertEquals($foo_time, $driver->getMax(['foo', $time]));
    }

    /**
     * Get test driver
     *
     * @return \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
     */
    abstract protected function getDriver();
}