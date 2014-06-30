<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Utility\Shmop as ShmopUtility;

/**
 * Test shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class ShmopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function setUp()
    {
        $sh = new ShmopUtility(Shmop::getIdBykey('foo'));
        $sh->delete();
    }

    /**
     * Test get null
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::getIdBykey
     */
    public function testGetNull()
    {
        $obj = new Shmop();
        $this->assertNull($obj->get('foo'));
    }

    /**
     * Test get
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::getIdBykey
     */
    public function testGet()
    {
        $time = new \DateTime();
        $obj = new Shmop();
        $this->assertTrue($obj->set('foo', $time));
        $this->assertEquals($time, $obj->get('foo'));
        $this->assertNotEquals($time, $obj->get('foo')->modify('+1 day'));
    }

    /**
     * Test set
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::getIdBykey
     */
    public function testSet()
    {
        $time = new \DateTime();
        $obj = new Shmop();
        $this->assertTrue($obj->set('foo', $time));
        $this->assertTrue($obj->set('foo', $time->modify('-1 day')));
    }

    /**
     * Test sync list times
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::set
     */
    public function testSync()
    {
        $time = new \DateTime();
        $first = new Shmop();
        $first->set('foo', $time);

        // new object
        $second = new Shmop();
        $this->assertEquals($time, $second->get('foo'));
    }

    /**
     * Test save
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::save
     */
    public function testSave()
    {
        $obj = new Shmop();
        $this->assertTrue($obj->save());
    }

    /**
     * Test get max empty params
     *
     * @expectedException InvalidArgumentException
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::getMax
     */
    public function testGetMaxEmpty()
    {
        $obj = new Shmop();
        $obj->getMax([]);
        $obj->getMax([null]);
    }

    /**
     * Test get max not scalar params
     *
     * @expectedException InvalidArgumentException
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::getMax
     */
    public function testGetMaxNotScalar()
    {
        $obj = new Shmop();
        $obj->getMax([null]);
    }

    /**
     * Test get max
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop::getMax
     */
    public function testGetMax()
    {
        $time = new \DateTime();
        $obj = new Shmop();
        $this->assertEquals($time, $obj->getMax([$time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');
        $obj->set('foo', $foo_time);
        $this->assertEquals($foo_time, $obj->getMax(['foo', $time]));
    }
}