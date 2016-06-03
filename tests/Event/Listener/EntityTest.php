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
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\Demo;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Keeper
     */
    protected $keeper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LifecycleEventArgs
     */
    protected $args;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Configuration
     */
    protected $conf;

    /**
     * @var Entity
     */
    protected $listener;

    protected function setUp()
    {
        $this->keeper = $this->getMockObject(Keeper::class);
        
        $this->conf = $this->getMockObject(Configuration::class);

        /* @var $em \PHPUnit_Framework_MockObject_MockObject|EntityManager */
        $em = $this->getMockObject(EntityManager::class);
        $em
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($this->conf));

        $this->args = $this->getMockObject(LifecycleEventArgs::class);
        $this->args
            ->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(new Demo()));

        $this->listener = new Entity($this->keeper);
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return [
            ['postPersist'],
            ['postRemove'],
            ['postUpdate']
        ];
    }

    /**
     * @dataProvider getMethods
     *
     * @param string $method
     */
    public function testHandleEvent($method)
    {
        $this->keeper
            ->expects($this->once())
            ->method('set')
            ->with('AnimeDbCacheTimeKeeperBundle:Demo', $this->isInstanceOf('DateTime'));

        $this->conf
            ->expects($this->once())
            ->method('getEntityNamespaces')
            ->will($this->returnValue([
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity',
                'AnimeDbCacheTimeKeeperBundle' => 'AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity'
            ]));

        call_user_func([$this->listener, $method], $this->args);
    }

    /**
     * @dataProvider getMethods
     * @expectedException \RuntimeException
     *
     * @param string $method
     */
    public function testHandleEventFailed($method)
    {
        $this->keeper
            ->expects($this->never())
            ->method('set');

        $this->conf
            ->expects($this->once())
            ->method('getEntityNamespaces')
            ->will($this->returnValue([
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity'
            ]));

        call_user_func([$this->listener, $method], $this->args);
    }
}
