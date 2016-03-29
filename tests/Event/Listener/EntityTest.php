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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\Demo;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Test entity event listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
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
     * @var Entity
     */
    protected $listener;

    protected function setUp()
    {
        $this->keeper = $this->getMockObject(Keeper::class);
        $this->keeper
            ->expects($this->once())
            ->method('set')
            ->with('AnimeDbCacheTimeKeeperBundle:Demo', $this->isInstanceOf('DateTime'));

        $conf = $this->getMockObject(Configuration::class);
        $conf
            ->expects($this->once())
            ->method('getEntityNamespaces')
            ->will($this->returnValue([
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity',
                'AnimeDbCacheTimeKeeperBundle' => 'AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity'
            ]));

        $em = $this->getMockObject(EntityManager::class);
        $em
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($conf));

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

    public function testPostPersist()
    {
        $this->listener->postPersist($this->args);
    }

    public function testPostRemove()
    {
        $this->listener->postRemove($this->args);
    }

    public function testPostUpdate()
    {
        $this->listener->postUpdate($this->args);
    }
}
