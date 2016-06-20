<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Exception\NotModifiedException;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\ResponseConfigurator;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class KeeperTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DriverInterface
     */
    protected $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResponseConfigurator
     */
    protected $configurator;

    /**
     * @var Keeper
     */
    protected $keeper;
    /**
     * @var \DateTime
     */
    protected $time;

    protected function setUp()
    {
        $this->time = new \DateTime();
        $this->driver = $this->getMock(DriverInterface::class);
        $this->configurator = $this->getNoConstructorMock(ResponseConfigurator::class);

        $this->keeper = new Keeper($this->driver, $this->configurator, true);
    }

    public function testGet()
    {
        $this->driver
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->time));

        $this->assertEquals($this->time, $this->keeper->get('foo'));
    }

    public function testGetEmptyLastUpdate()
    {
        $this->driver
            ->expects($this->once())
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue(null));
        $this->driver
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, $this->time);

        $this->assertEquals($this->time, $this->keeper->get(Keeper::LAST_UPDATE_KEY));
    }

    public function testGetEmpty()
    {
        $this->driver
            ->expects($this->at(0))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $this->driver
            ->expects($this->at(1))
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue($this->time));

        $this->assertEquals($this->time, $this->keeper->get('foo'));
    }

    public function testSet()
    {
        $this->driver
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));

        $this->assertTrue($this->keeper->set('foo', $this->time));
    }

    public function testRemove()
    {
        $this->driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));

        $this->assertTrue($this->keeper->remove('foo'));
    }

    public function testRemoveFail()
    {
        $this->driver
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(false));

        $this->assertFalse($this->keeper->remove('foo'));
    }

    /**
     * @return array
     */
    public function getMax()
    {
        return [
            [null],
            ['foo'],
            [['foo']],
            [['foo', Keeper::LAST_UPDATE_KEY]],
        ];
    }

    /**
     * @dataProvider getMax
     *
     * @param mixed $params
     */
    public function testGetMax($params)
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with($params ? ['foo', Keeper::LAST_UPDATE_KEY] : [Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        $this->assertEquals($this->time, $params ? $this->keeper->getMax($params) : $this->keeper->getMax());
    }

    /**
     * @dataProvider getMax
     *
     * @param mixed $params
     */
    public function testGetMaxEmptyList($params)
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with($params ? ['foo', Keeper::LAST_UPDATE_KEY] : [Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue(null));
        $this->driver
            ->expects($this->once())
            ->method('set')
            ->will($this->returnCallback(function ($key, $time) {
                $this->assertEquals(Keeper::LAST_UPDATE_KEY, $key);
                $this->assertInstanceOf('\DateTime', $time);
            }));

        $this->assertInstanceOf('\DateTime', $params ? $this->keeper->getMax($params) : $this->keeper->getMax());
    }

    public function testGetMaxDisabled()
    {
        $this->driver
            ->expects($this->never())
            ->method('getMax');

        $this->keeper = new Keeper($this->driver, $this->configurator, false);

        $this->assertInstanceOf('\DateTime', $this->keeper->getMax(['foo', 'bar']));
    }

    public function testGetResponse()
    {
        $lifetime = 3600;
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        $response = $this->getMock(Response::class);
        $configured_response = $this->getMock(Response::class);

        $this->configurator
            ->expects($this->once())
            ->method('configure')
            ->with($response, $this->time, $lifetime)
            ->will($this->returnValue($configured_response));

        $this->assertEquals($configured_response, $this->keeper->getResponse(['foo'], $lifetime, $response));
    }

    public function testGetResponseEmpty()
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with([Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        $response = new Response();

        $configured_response = new Response();
        $configured_response->setLastModified($this->time);

        $this->configurator
            ->expects($this->once())
            ->method('configure')
            ->with($response, $this->time)
            ->will($this->returnValue($configured_response));

        $this->assertEquals($configured_response, $this->keeper->getResponse());
    }

    public function testGetResponseDisabled()
    {
        $this->driver
            ->expects($this->never())
            ->method('getMax');

        $this->configurator
            ->expects($this->never())
            ->method('configure');

        $this->keeper = new Keeper($this->driver, $this->configurator, false);
        $response = new Response();

        $this->assertEquals($response, $this->keeper->getResponse(['foo'], 3600));
    }

    public function testGetResponseDisabledEmpty()
    {
        $this->driver
            ->expects($this->never())
            ->method('getMax');

        $this->configurator
            ->expects($this->never())
            ->method('configure');

        $this->keeper = new Keeper($this->driver, $this->configurator, false);
        $response = new Response();

        $this->assertEquals($response, $this->keeper->getResponse());
    }

    public function testGetModifiedResponseNoParams()
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with([Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        /** @var $request \PHPUnit_Framework_MockObject_MockObject|Request */
        $request = $this->getMock(Request::class);
        $request
            ->expects($this->once())
            ->method('isMethodSafe')
            ->will($this->returnValue(false));

        $response = new Response();

        $configured_response = new Response();
        $configured_response->setLastModified($this->time);

        $this->configurator
            ->expects($this->once())
            ->method('configure')
            ->with($response, $this->time)
            ->will($this->returnValue($configured_response));

        $response = $this->keeper->getModifiedResponse($request);

        $this->assertEquals($configured_response, $response);
    }

    public function testGetModifiedResponseUseLifetime()
    {
        $lifetime = 3600;

        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        /** @var $request \PHPUnit_Framework_MockObject_MockObject|Request */
        $request = $this->getMock(Request::class);

        /** @var $response \PHPUnit_Framework_MockObject_MockObject|Response */
        $response = $this->getMock(Response::class);

        /** @var $configured_response \PHPUnit_Framework_MockObject_MockObject|Response */
        $configured_response = $this->getMock(Response::class);
        $configured_response
            ->expects($this->once())
            ->method('isNotModified')
            ->with($request)
            ->will($this->returnValue(false));

        $this->configurator
            ->expects($this->once())
            ->method('configure')
            ->with($response, $this->time)
            ->will($this->returnValue($configured_response));

        $response = $this->keeper->getModifiedResponse($request, 'foo', $lifetime, $response);

        $this->assertEquals($configured_response, $response);
    }

    public function testGetModifiedResponse()
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        /** @var $request \PHPUnit_Framework_MockObject_MockObject|Request */
        $request = $this->getMock(Request::class);

        /** @var $response \PHPUnit_Framework_MockObject_MockObject|Response */
        $response = $this->getMock(Response::class);

        /** @var $configured_response \PHPUnit_Framework_MockObject_MockObject|Response */
        $configured_response = $this->getMock(Response::class);
        $configured_response
            ->expects($this->once())
            ->method('isNotModified')
            ->with($request)
            ->will($this->returnValue(true));
        $configured_response
            ->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(Response::HTTP_NOT_MODIFIED));

        $this->configurator
            ->expects($this->once())
            ->method('configure')
            ->with($response, $this->time)
            ->will($this->returnValue($configured_response));

        try {
            $this->keeper->getModifiedResponse($request, 'foo', -1, $response);
            $this->assertTrue(false, 'Must throw exception');
        } catch (NotModifiedException $e) {
            $this->assertEquals($configured_response, $e->getResponse());
            $this->assertEquals(Response::HTTP_NOT_MODIFIED, $e->getCode());
            $this->assertEquals(Response::$statusTexts[Response::HTTP_NOT_MODIFIED], $e->getMessage());
        }
    }
}
