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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy;

/**
 * Test dummy driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class DummyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test init
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::__construct
     */
    public function testInit()
    {
        $obj = new Dummy();
        $this->assertInstanceOf('DateTime', $obj->get('test'));
    }

    /**
     * Test get
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::get
     */
    public function testGet()
    {
        $time = new \DateTime();
        $obj = new Dummy($time);

        $this->assertEquals($time, $obj->get('test'));
        $this->assertNotEquals($time->modify('+1 day'), $obj->get('test'));
    }

    /**
     * Test modify get
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::get
     */
    public function testModifyGet()
    {
        $time = new \DateTime();
        $obj = new Dummy($time);

        $this->assertNotEquals($time, $obj->get('test')->modify('+1 day'));
        $this->assertEquals($time, $obj->get('test'));
    }

    /**
     * Test set
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::set
     */
    public function testSet()
    {
        $obj = new Dummy();
        $this->assertTrue($obj->set('foo', new \DateTime()));
    }

    /**
     * Test get max
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::getMax
     */
    public function testGetMax()
    {
        $time = new \DateTime();
        $obj = new Dummy($time);

        $this->assertEquals($time, $obj->getMax([]));
        $this->assertNotEquals($time, $obj->getMax([])->modify('+1 day'));
    }

    /**
     * Test save
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy::save
     */
    public function testSave()
    {
        $obj = new Dummy();
        $this->assertTrue($obj->save());
    }
}