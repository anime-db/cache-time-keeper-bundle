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
use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\ConsoleListener;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class ConsoleListenerTest extends TestCase
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

    protected function setUp()
    {
        $this->keeper = $this->getNoConstructorMock(Keeper::class);
        $this->command = $this->getNoConstructorMock(Command::class);
        $this->event = $this->getNoConstructorMock(ConsoleTerminateEvent::class);
    }

    public function testOnTerminateNotCacheClear()
    {
        $track_clear_cache = true;

        $this->keeper
            ->expects($this->never())
            ->method('set');

        $this->event
            ->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($this->command));

        $this->command
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $listener = new ConsoleListener($this->keeper, $track_clear_cache);
        $listener->onTerminate($this->event);
    }

    public function testOnTerminateNoTrack()
    {
        $track_clear_cache = false;

        $this->keeper
            ->expects($this->never())
            ->method('set');

        $this->event
            ->expects($this->never())
            ->method('getCommand');

        $listener = new ConsoleListener($this->keeper, $track_clear_cache);
        $listener->onTerminate($this->event);
    }

    public function testOnTerminate()
    {
        $track_clear_cache = true;

        $this->keeper
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, new \DateTime());

        $this->event
            ->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($this->command));

        $this->command
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('cache:clear'));

        $listener = new ConsoleListener($this->keeper, $track_clear_cache);
        $listener->onTerminate($this->event);
    }
}
