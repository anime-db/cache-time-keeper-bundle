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
     * Metadata dir
     *
     * @var string
     */
    protected $dir;

    /**
     * Construct
     */
    public function setUp()
    {
        $this->dir = sys_get_temp_dir().'/unit-test.meta/';
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755);
        }
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        if (is_dir($this->dir)) {
            foreach (scandir($this->dir) as $value) {
                if ($value[0] != '.') {
                    @unlink($this->dir.'/'.$value);
                }
            }
            rmdir($this->dir);
        }
    }

    /**
     * Test get null
     */
    public function testGetNull()
    {
        $obj = new File($this->dir);
        $this->assertNull($obj->get('foo'));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $time = new \DateTime();
        $obj = new File($this->dir);
        $this->assertTrue($obj->set('foo', $time));
        $this->assertEquals($time, $obj->get('foo'));
        $this->assertNotEquals($time, $obj->get('foo')->modify('+1 day'));
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $time = new \DateTime();
        $obj = new File($this->dir);
        $this->assertTrue($obj->set('foo', $time));
        $this->assertTrue($obj->set('foo', $time->modify('-1 day')));
    }

    /**
     * Test sync list times
     */
    public function testSync()
    {
        $time = new \DateTime();
        $first = new File($this->dir);
        $first->set('foo', $time);

        // new object
        $second = new File($this->dir);
        $this->assertEquals($time, $second->get('foo'));
    }

    /**
     * Test get max empty params
     *
     * @expectedException InvalidArgumentException
     */
    public function testGetMaxEmpty()
    {
        $obj = new File($this->dir);
        $obj->getMax([]);
    }

    /**
     * Test get max not scalar params
     *
     * @expectedException InvalidArgumentException
     */
    public function testGetMaxNotScalar()
    {
        $obj = new File($this->dir);
        $obj->getMax([null]);
    }

    /**
     * Test get max
     */
    public function testGetMax()
    {
        $time = new \DateTime();
        $obj = new File($this->dir);
        $this->assertEquals($time, $obj->getMax([$time]));

        $foo_time = new \DateTime();
        $foo_time->modify('+1 day');
        $obj->set('foo', $foo_time);
        $this->assertEquals($foo_time, $obj->getMax(['foo', $time]));
    }
}