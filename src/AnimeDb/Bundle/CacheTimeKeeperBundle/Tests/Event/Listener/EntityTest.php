<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity;

/**
 * Test entity event listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Event\Listener
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test post persist
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::postPersist
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::getKeyFromEntity
     */
    public function testPostPersist()
    {
        $obj = new Entity($this->getKeeper());
        $obj->postPersist($this->getEventMock());
    }

    /**
     * Test post remove
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::postRemove
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::getKeyFromEntity
     */
    public function testPostRemove()
    {
        $obj = new Entity($this->getKeeper());
        $obj->postRemove($this->getEventMock());
    }

    /**
     * Test post update
     *
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::__construct
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::postUpdate
     * @covers \AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\Entity::getKeyFromEntity
     */
    public function testPostUpdate()
    {
        $obj = new Entity($this->getKeeper());
        $obj->postUpdate($this->getEventMock());
    }

    /**
     * Get event mock
     *
     * @return \Doctrine\ORM\Event\LifecycleEventArgs
     */
    protected function getEventMock()
    {
        $meta_mock = $this
            ->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $meta_mock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $em_mock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em_mock
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with('stdClass')
            ->will($this->returnValue($meta_mock));

        $args_mock = $this
            ->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $args_mock
            ->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em_mock));
        $args_mock
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(new \stdClass()));
        return $args_mock;
    }

    /**
     * Get keeper
     *
     * @return \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper
     */
    protected function getKeeper()
    {
        $keeper_mock = $this
            ->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper')
            ->disableOriginalConstructor()
            ->getMock();
        $keeper_mock
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->isInstanceOf('DateTime'));

        return $keeper_mock;
    }
}