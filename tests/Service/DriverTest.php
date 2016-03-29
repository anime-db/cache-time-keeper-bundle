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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;

/**
 * Test driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class DriverTest extends TestCase
{
    /**
     * @var \DateTime
     */
    protected $time;

    protected function setUp()
    {
        $this->time = new \DateTime();
    }

    public function testGetNull()
    {
        $this->assertNull($this->getDriver()->get('foo'));
    }

    public function testGet()
    {
        $driver = $this->getDriver();
        $this->assertTrue($driver->set('foo', $this->time));
        $this->assertEquals($this->time, $driver->get('foo'));
        $this->assertNotEquals($this->time, $driver->get('foo')->modify('+1 day'));
    }

    public function testSet()
    {
        $driver = $this->getDriver();
        $this->assertTrue($driver->set('foo', $this->time));
        $this->assertTrue($driver->set('foo', $this->time->modify('-1 day')));
    }

    public function testSync()
    {
        $first = $this->getDriver();
        $first->set('foo', $this->time);

        // new object
        $second = $this->getDriver();
        $this->assertEquals($this->time, $second->get('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetMaxEmpty()
    {
        $this->getDriver()->getMax([]);
    }

    public function testRemove()
    {
        $driver = $this->getDriver();
        $driver->set('foo', $this->time);
        $this->assertTrue($driver->remove('foo'));
        $this->assertEmpty($driver->get('foo'));
    }

    public function testRemoveFail()
    {
        $this->assertFalse($this->getDriver()->remove('foo'));
    }

    public function testGetMax()
    {
        $driver = $this->getDriver();
        $this->assertEquals($this->time, $driver->getMax([$this->time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');
        $driver->set('foo', $foo_time);
        $this->assertEquals($foo_time, $driver->getMax(['foo', $this->time]));
    }

    /**
     * @return Driver
     */
    abstract protected function getDriver();
}
