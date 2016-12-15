<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\ExceptionListener;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Exception\NotModifiedException;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|GetResponseForExceptionEvent
     */
    protected $event;

    /**
     * @var ExceptionListener
     */
    protected $listener;

    protected function setUp()
    {
        $this->event = $this->getNoConstructorMock(GetResponseForExceptionEvent::class);
        $this->listener = new ExceptionListener();
    }

    public function testOnKernelExceptionIgnore()
    {
        $this->event
            ->expects($this->once())
            ->method('getException')
            ->will($this->returnValue(new \RuntimeException()));

        $this->event
            ->expects($this->never())
            ->method('setResponse');

        $this->listener->onKernelException($this->event);
    }

    public function testOnKernelException()
    {
        $response = $this->getMock(Response::class);

        $exception = $this->getNoConstructorMock(NotModifiedException::class);
        $exception
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $this->event
            ->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exception));

        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->with($response);

        $this->listener->onKernelException($this->event);
    }
}
