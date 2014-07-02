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