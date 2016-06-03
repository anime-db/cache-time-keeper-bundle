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

    const TEST_DATA = 'foo';

    protected function setUp()
    {
        $this->time = new \DateTime();
    }

    public function testGetNull()
    {
        $this->assertNull($this->getDriver()->get(self::TEST_DATA));
    }

    public function testGet()
    {
        $driver = $this->getDriver();
        $this->assertTrue($driver->set(self::TEST_DATA, $this->time));
        $this->assertEquals($this->time, $driver->get(self::TEST_DATA));
        $this->assertNotEquals($this->time, $driver->get(self::TEST_DATA)->modify('+1 day'));
    }

    public function testSet()
    {
        $driver = $this->getDriver();
        $this->assertTrue($driver->set(self::TEST_DATA, $this->time));
        $this->assertTrue($driver->set(self::TEST_DATA, $this->time->modify('-1 day')));
    }

    public function testSync()
    {
        $first = $this->getDriver();
        $first->set(self::TEST_DATA, $this->time);

        // new object
        $second = $this->getDriver();
        $this->assertEquals($this->time, $second->get(self::TEST_DATA));
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
        $driver->set(self::TEST_DATA, $this->time);
        $this->assertTrue($driver->remove(self::TEST_DATA));
        $this->assertEmpty($driver->get(self::TEST_DATA));
    }

    public function testRemoveFail()
    {
        $this->assertFalse($this->getDriver()->remove(self::TEST_DATA));
    }

    public function testGetMax()
    {
        $driver = $this->getDriver();
        $this->assertEquals($this->time, $driver->getMax([$this->time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');
        $driver->set(self::TEST_DATA, $foo_time);
        $this->assertEquals($foo_time, $driver->getMax([self::TEST_DATA, $this->time]));
    }

    /**
     * @return DriverInterface
     */
    abstract protected function getDriver();
}
