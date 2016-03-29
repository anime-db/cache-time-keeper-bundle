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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\Demo;

/**
 * Test entity event listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Entity
     */
    protected $listener;

    protected function setUp()
    {
        $this->listener = new Entity($this->getKeeper());
    }

    public function testPostPersist()
    {
        $this->listener->postPersist($this->getEventMock());
    }

    public function testPostRemove()
    {
        $this->listener->postRemove($this->getEventMock());
    }

    public function testPostUpdate()
    {
        $this->listener->postUpdate($this->getEventMock());
    }

    /**
     * @return LifecycleEventArgs
     */
    protected function getEventMock()
    {
        $conf = $this->getMockBuilder('\Doctrine\ORM\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $conf
            ->expects($this->once())
            ->method('getEntityNamespaces')
            ->willReturn([
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity',
                'AnimeDbCacheTimeKeeperBundle' => 'AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity'
            ]);

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($conf);

        $args = $this->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $args
            ->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($em);
        $args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn(new Demo());

        return $args;
    }

    /**
     * @return Keeper
     */
    protected function getKeeper()
    {
        $keeper = $this->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper')
            ->disableOriginalConstructor()
            ->getMock();
        $keeper
            ->expects($this->once())
            ->method('set')
            ->with('AnimeDbCacheTimeKeeperBundle:Demo', $this->isInstanceOf('DateTime'));

        return $keeper;
    }
}
