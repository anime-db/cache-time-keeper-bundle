<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Request;

/**
 * Test request event listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Event\Listener
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test on terminate
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Request::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Request::onTerminate
     */
    public function testOnTerminate()
    {
        $driver_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver')
            ->getMock();
        $driver_mock
            ->expects($this->once())
            ->method('save');

        $obj = new Request($driver_mock);
        $obj->onTerminate();
    }
}