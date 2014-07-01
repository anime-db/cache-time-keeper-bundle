<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Multi;

/**
 * Test multi driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class MultiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Fast mock
     *
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $fast_mock;

    /**
     * Fast mock
     *
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $slow_mock;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->fast_mock = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver');
        $this->slow_mock = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver');
    }

    /**
     * Test get fast
     */
    public function testGetFast()
    {
        $time = new \DateTime();
        $this->fast_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($time));
        $this->slow_mock
            ->expects($this->never())
            ->method('get');

        $this->assertEquals($time, $this->getDriver()->get('foo'));
    }

    /**
     * Test get slow
     */
    public function testGetSlow()
    {
        $time = new \DateTime();
        $this->fast_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $this->slow_mock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($time));

        $this->assertEquals($time, $this->getDriver()->get('foo'));
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $time = new \DateTime();
        $this->fast_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $time)
            ->will($this->returnValue(true));
        $this->slow_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $time)
            ->will($this->returnValue(true));

        $this->assertTrue($this->getDriver()->set('foo', $time));
    }

    /**
     * Test set fail
     */
    public function testSetFail()
    {
        $time = new \DateTime();
        $this->fast_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $time)
            ->will($this->returnValue(false));
        $this->slow_mock
            ->expects($this->never())
            ->method('set');

        $this->assertFalse($this->getDriver()->set('foo', $time));
    }

    /**
     * Get driver
     *
     * @return \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Multi
     */
    protected function getDriver()
    {
        return new Multi($this->fast_mock, $this->slow_mock);
    }
}