<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Exception;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Exception\NotModifiedException;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class NotModifiedExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $status_code = 500;
        $code = 123;

        /** @var $response \PHPUnit_Framework_MockObject_MockObject|Response */
        $response = $this->getMock(Response::class);
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($status_code));


        $previous = $this->getMock(\Exception::class);

        $exception = new NotModifiedException($response, $code, $previous);

        $this->assertEquals(Response::$statusTexts[$status_code], $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
        $this->assertEquals($response, $exception->getResponse());
    }

    public function testConstructOnlyResponse()
    {
        $status_code = 500;

        /** @var $response \PHPUnit_Framework_MockObject_MockObject|Response */
        $response = $this->getMock(Response::class);
        $response
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue($status_code));


        $exception = new NotModifiedException($response);

        $this->assertEquals(Response::$statusTexts[$status_code], $exception->getMessage());
        $this->assertEquals($status_code, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}