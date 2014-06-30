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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File;

/**
 * Test file driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Metadata file
     *
     * @var string
     */
    protected $filename;

    /**
     * Construct
     */
    public function setUp()
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'unit-test.meta');
        unlink($this->filename);
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        @unlink($this->filename);
    }

    /**
     * Test get null
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::__destruct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::load
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::save
     */
    public function testGetNull()
    {
        $obj = new File($this->filename);
        $this->assertNull($obj->get('foo'));
    }

    /**
     * Test get
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::load
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::save
     */
    public function testGet()
    {
        $time = new \DateTime();
        $obj = new File($this->filename);
        $this->assertTrue($obj->set('foo', $time));
        $this->assertEquals($time, $obj->get('foo'));
        $this->assertNotEquals($time, $obj->get('foo')->modify('+1 day'));
    }

    /**
     * Test save
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::load
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::save
     */
    public function testSave()
    {
        $time = new \DateTime();
        $obj = new File($this->filename);
        $this->assertTrue($obj->save());
        $obj->set('foo', $time);
        $this->assertTrue($obj->save());
        $this->assertEquals($time, $obj->get('foo'));
    }

    /**
     * Test load
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::load
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::save
     */
    public function testLoad()
    {
        $time = new \DateTime();
        $obj = new File($this->filename);
        $obj->set('foo', $time);
        $obj->save();
        unset($obj);

        // new object
        $obj = new File($this->filename);
        $this->assertEquals($time, $obj->get('foo'));
    }

    /**
     * Test sync list
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::set
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::load
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::save
     */
    public function testSync()
    {
        $time = new \DateTime();
        $first = new File($this->filename);
        $first->set('foo', $time);

        // new object
        $second = new File($this->filename);
        $this->assertEquals($time, $second->get('foo'));
    }

    /**
     * Test get max empty params
     *
     * @expectedException InvalidArgumentException
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::getMax
     */
    public function testGetMaxEmpty()
    {
        $obj = new File($this->filename);
        $obj->getMax([]);
        $obj->getMax([null]);
    }

    /**
     * Test get max not scalar params
     *
     * @expectedException InvalidArgumentException
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::getMax
     */
    public function testGetMaxNotScalar()
    {
        $obj = new File($this->filename);
        $obj->getMax([null]);
    }

    /**
     * Test get max
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::get
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File::getMax
     */
    public function testGetMax()
    {
        $time = new \DateTime();
        $obj = new File($this->filename);
        $this->assertEquals($time, $obj->getMax([$time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');
        $obj->set('foo', $foo_time);
        $this->assertEquals($foo_time, $obj->getMax(['foo', $time]));
    }
}