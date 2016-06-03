<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface;
use Symfony\Component\HttpFoundation\Response;

class KeeperTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DriverInterface
     */
    protected $driver;

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

        $this->keeper = new Keeper($this->driver);
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
            [['foo', Keeper::LAST_UPDATE_KEY]]
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
        $that = $this;
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with($params ? ['foo', Keeper::LAST_UPDATE_KEY] : [Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue(null));
        $this->driver
            ->expects($this->once())
            ->method('set')
            ->will($this->returnCallback(function ($key, $time) use ($that) {
                $that->assertEquals(Keeper::LAST_UPDATE_KEY, $key);
                $this->assertInstanceOf('\DateTime', $time);
            }));

        $this->assertInstanceOf('\DateTime', $params ? $this->keeper->getMax($params) : $this->keeper->getMax());
    }

    public function testGetResponse()
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));
        $lifetime = 3600;
        $response = $this->getMock(Response::class);
        $response
            ->expects($this->once())
            ->method('setPublic')
            ->will($this->returnSelf());
        $response
            ->expects($this->once())
            ->method('setMaxAge')
            ->with($lifetime)
            ->will($this->returnSelf());
        $response
            ->expects($this->once())
            ->method('setSharedMaxAge')
            ->with($lifetime)
            ->will($this->returnSelf());
        $response
            ->expects($this->once())
            ->method('setExpires')
            ->with((new \DateTime())->modify('+'.$lifetime.' seconds'))
            ->will($this->returnSelf());
        $response
            ->expects($this->once())
            ->method('setLastModified')
            ->with($this->time)
            ->will($this->returnSelf());

        $this->assertEquals($response, $this->keeper->getResponse(['foo'], $lifetime, $response));
    }

    public function testGetResponseEmpty()
    {
        $this->driver
            ->expects($this->once())
            ->method('getMax')
            ->with([Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));
        $response = new Response();
        $response->setPublic()->setLastModified($this->time);

        $this->assertEquals($response, $this->keeper->getResponse());
    }
}
