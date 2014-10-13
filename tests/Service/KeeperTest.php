<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test keeper
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class KeeperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Time
     *
     * @var \DateTime
     */
    protected $time;

    /**
     * Driver mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver_mock;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->time = new \DateTime();
        $this->driver_mock = $this->getMock('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface');
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->time));

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->get('foo'));
    }

    /**
     * Test get empty last update
     */
    public function testGetEmptyLastUpdate()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue(null));
        $this->driver_mock
            ->expects($this->once())
            ->method('set')
            ->with(Keeper::LAST_UPDATE_KEY, $this->time);

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->get(Keeper::LAST_UPDATE_KEY));
    }

    /**
     * Test get empty
     */
    public function testGetEmpty()
    {
        $this->driver_mock
            ->expects($this->at(0))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue(null));
        $this->driver_mock
            ->expects($this->at(1))
            ->method('get')
            ->with(Keeper::LAST_UPDATE_KEY)
            ->will($this->returnValue($this->time));

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $obj->get('foo'));
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->time)
            ->will($this->returnValue(true));

        $obj = new Keeper($this->driver_mock);
        $this->assertTrue($obj->set('foo', $this->time));
    }

    /**
     * Test remove time
     */
    public function testRemove()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(true));

        $obj = new Keeper($this->driver_mock);
        $this->assertTrue($obj->remove('foo'));
    }

    /**
     * Test remove time fail
     */
    public function testRemoveFail()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('remove')
            ->with('foo')
            ->will($this->returnValue(false));

        $obj = new Keeper($this->driver_mock);
        $this->assertFalse($obj->remove('foo'));
    }

    /**
     * Get max time by keys
     *
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
     * Test get max
     *
     * @dataProvider getMax
     *
     * @param mixed $params
     */
    public function testGetMax($params)
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('getMax')
            ->with($params ? ['foo', Keeper::LAST_UPDATE_KEY] : [Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($this->time, $params ? $obj->getMax($params) : $obj->getMax());
    }

    /**
     * Test get max from empty list
     *
     * @dataProvider getMax
     *
     * @param mixed $params
     */
    public function testGetMaxEmptyList($params)
    {
        $that = $this;
        $this->driver_mock
            ->expects($this->once())
            ->method('getMax')
            ->with($params ? ['foo', Keeper::LAST_UPDATE_KEY] : [Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue(null));
        $this->driver_mock
            ->expects($this->once())
            ->method('set')
            ->willReturnCallback(function ($key, $time) use ($that) {
                $that->assertEquals(Keeper::LAST_UPDATE_KEY, $key);
                $this->assertInstanceOf('\DateTime', $time);
            });

        $obj = new Keeper($this->driver_mock);
        $this->assertInstanceOf('\DateTime', $params ? $obj->getMax($params) : $obj->getMax());
    }

    /**
     * Test get response
     */
    public function testGetResponse()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('getMax')
            ->with(['foo', Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));
        $lifetime = 3600;
        $response = $this->getMock('\Symfony\Component\HttpFoundation\Response');
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

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($response, $obj->getResponse(['foo'], $lifetime, $response));
    }

    /**
     * Test get response
     */
    public function testGetResponseEmpty()
    {
        $this->driver_mock
            ->expects($this->once())
            ->method('getMax')
            ->with([Keeper::LAST_UPDATE_KEY])
            ->will($this->returnValue($this->time));
        $response = new Response();
        $response->setPublic()->setLastModified($this->time);

        $obj = new Keeper($this->driver_mock);
        $this->assertEquals($response, $obj->getResponse());
    }
}
