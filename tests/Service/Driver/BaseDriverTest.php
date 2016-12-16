<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;

abstract class BaseDriverTest extends TestCase
{
    /**
     * @var \DateTime
     */
    protected $time;

    const DATE_KEY = 'foo';

    protected function setUp()
    {
        $this->time = new \DateTime('2016-12-16 16:29:13');
    }

    public function testGetNull()
    {
        $this->assertNull($this->getDriver()->get(self::DATE_KEY));
    }

    public function testGet()
    {
        $driver = $this->getDriver();
        $this->assertTrue($driver->set(self::DATE_KEY, $this->time));
        $this->assertNotEquals($this->time, $driver->get(self::DATE_KEY)->modify('+1 day'));
        $this->assertEquals($this->time, $driver->get(self::DATE_KEY));
    }

    public function testSet()
    {
        $driver = $this->getDriver();
        $this->assertTrue($driver->set(self::DATE_KEY, $this->time));
        $this->assertTrue($driver->set(self::DATE_KEY, $this->time->modify('-1 day')));
    }

    public function testSync()
    {
        $first = $this->getDriver();
        $first->set(self::DATE_KEY, $this->time);

        // new object
        $second = $this->getDriver();
        $this->assertEquals($this->time, $second->get(self::DATE_KEY));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMaxEmpty()
    {
        $this->getDriver()->getMax([]);
    }

    public function testRemove()
    {
        $driver = $this->getDriver();
        $driver->set(self::DATE_KEY, $this->time);
        $this->assertTrue($driver->remove(self::DATE_KEY));
        $this->assertEmpty($driver->get(self::DATE_KEY));
    }

    public function testRemoveFail()
    {
        $this->assertFalse($this->getDriver()->remove(self::DATE_KEY));
    }

    public function testGetMax()
    {
        $driver = $this->getDriver();
        $this->assertEquals($this->time, $driver->getMax([$this->time]));

        $foo_time = clone $this->time;
        $foo_time->modify('+1 day');
        $driver->set(self::DATE_KEY, $foo_time);
        $this->assertEquals($foo_time, $driver->getMax([self::DATE_KEY, $this->time]));
    }

    /**
     * @return DriverInterface
     */
    abstract protected function getDriver();
}
