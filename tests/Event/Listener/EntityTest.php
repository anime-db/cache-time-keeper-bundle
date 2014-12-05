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

/**
 * Test entity event listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test post persist
     */
    public function testPostPersist()
    {
        $obj = new Entity($this->getKeeper());
        $obj->postPersist($this->getEventMock());
    }

    /**
     * Test post remove
     */
    public function testPostRemove()
    {
        $obj = new Entity($this->getKeeper());
        $obj->postRemove($this->getEventMock());
    }

    /**
     * Test post update
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
        $meta = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $meta
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with('stdClass')
            ->willReturn($meta);

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
            ->willReturn(new \stdClass());
        return $args;
    }

    /**
     * Get keeper
     *
     * @return \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper
     */
    protected function getKeeper()
    {
        $keeper = $this->getMockBuilder('\AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper')
            ->disableOriginalConstructor()
            ->getMock();
        $keeper
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->isInstanceOf('DateTime'));

        return $keeper;
    }
}
