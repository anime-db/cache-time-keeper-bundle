<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\DoctrineListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Keeper
     */
    protected $keeper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CacheKeyBuilder
     */
    protected $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LifecycleEventArgs
     */
    protected $args;

    /**
     * @var \stdClass
     */
    protected $entity;

    protected function setUp()
    {
        $this->entity = new \stdClass();

        $this->keeper = $this->getNoConstructorMock(Keeper::class);
        $this->builder = $this->getNoConstructorMock(CacheKeyBuilder::class);

        $this->args = $this->getNoConstructorMock(LifecycleEventArgs::class);
        $this->args
            ->expects($this->atLeastOnce())
            ->method('getEntity')
            ->will($this->returnValue($this->entity));
    }

    /**
     * @return array
     */
    public function getTrackMethods()
    {
        return [
            ['postPersist', false, false, []],
            ['postRemove', false, true, []],
            ['postUpdate', false, false, []],
            ['postPersist', true, false, [1]],
            ['postRemove', true, true, [2]],
            ['postUpdate', true, false, [3]],
            ['postPersist', true, false, [1, 'foo']],
            ['postRemove', true, true, [2, 'bar']],
            ['postUpdate', true, false, [3, 'baz']],
        ];
    }

    /**
     * @dataProvider getTrackMethods
     *
     * @param string $method
     * @param bool $track_individually
     * @param bool $remove
     * @param array $ids
     */
    public function testHandleEvent($method, $track_individually, $remove, array $ids)
    {
        $alias = 'foo';

        $this->keeper
            ->expects($this->at(0))
            ->method('set')
            ->with($alias, $this->isInstanceOf('DateTime'));

        $this->builder
            ->expects($this->once())
            ->method('getEntityAlias')
            ->with($this->entity)
            ->will($this->returnValue($alias));

        if ($track_individually) {
            $suffix = implode(',', $ids);
            $this->builder
                ->expects($this->once())
                ->method('getEntityIdentifier')
                ->with($this->entity)
                ->will($this->returnValue($suffix));

            if ($suffix) {
                if ($remove) {
                    $this->keeper
                        ->expects($this->once())
                        ->method('remove')
                        ->with($alias.$suffix);
                } else {
                    $this->keeper
                        ->expects($this->at(1))
                        ->method('set')
                        ->with($alias.$suffix, $this->isInstanceOf('DateTime'));
                }
            }
        } else {
            $this->builder
                ->expects($this->never())
                ->method('getEntityIdentifier');
        }

        $listener = new DoctrineListener($this->keeper, $this->builder, $track_individually);
        call_user_func([$listener, $method], $this->args);
    }
}
