<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Console;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class ConsoleTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Keeper
     */
    protected $keeper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Command
     */
    protected $command;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ConsoleTerminateEvent
     */
    protected $event;

    /**
     * @var Console
     */
    protected $listener;

    protected function setUp()
    {
        $this->keeper = $this->getMockObject(Keeper::class);
        $this->command = $this->getMockObject(Command::class);
        $this->event = $this->getMockObject(ConsoleTerminateEvent::class);

        $this->event
            ->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($this->command));

        $this->listener = new Console($this->keeper);
    }

    public function testOnTerminate()
    {
        $this->command
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->listener->onTerminate($this->event);
    }

    public function testOnTerminateCache()
    {
        $this->keeper
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, new \DateTime());

        $this->command
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('cache:clear'));

        $this->listener->onTerminate($this->event);
    }
}
