<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Console;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;

/**
 * Test console event listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test on terminate
     */
    public function testOnTerminate()
    {
        $keeper_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper')
            ->disableOriginalConstructor()
            ->getMock();

        $command_mock = $this
            ->getMockBuilder('\Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()
            ->getMock();
        $command_mock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $event_mock = $this
            ->getMockBuilder('\Symfony\Component\Console\Event\ConsoleTerminateEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event_mock
            ->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($command_mock));

        $obj = new Console($keeper_mock);
        $obj->onTerminate($event_mock);
    }

    /**
     * Test on terminate cache
     */
    public function testOnTerminateCache()
    {
        $keeper_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper')
            ->disableOriginalConstructor()
            ->getMock();
        $keeper_mock
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, new \DateTime());

        $command_mock = $this
            ->getMockBuilder('\Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()
            ->getMock();
        $command_mock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('cache:clear'));

        $event_mock = $this
            ->getMockBuilder('\Symfony\Component\Console\Event\ConsoleTerminateEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event_mock
            ->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($command_mock));

        $obj = new Console($keeper_mock);
        $obj->onTerminate($event_mock);
    }
}