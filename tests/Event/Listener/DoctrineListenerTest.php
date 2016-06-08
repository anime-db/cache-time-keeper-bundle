<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\SubNs\Bar;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener\DoctrineListener;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\Foo;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineListenerTest extends TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    protected $em;

    protected function setUp()
    {
        $this->keeper = $this->getNoConstructorMock(Keeper::class);

        $this->conf = $this->getNoConstructorMock(Configuration::class);

        $this->em = $this->getNoConstructorMock(EntityManager::class);
        $this->em
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($this->conf));

        $this->args = $this->getNoConstructorMock(LifecycleEventArgs::class);
        $this->args
            ->expects($this->atLeastOnce())
            ->method('getEntityManager')
            ->will($this->returnValue($this->em));
    }

    /**
     * @return array
     */
    public function getTrackMethods()
    {
        return [
            ['postPersist', Foo::class, 'Foo', false, false, []],
            ['postRemove', Foo::class, 'Foo', false, true, []],
            ['postUpdate', Foo::class, 'Foo', false, false, []],
            ['postPersist', Bar::class, 'SubNs\Bar', false, false, []],
            ['postRemove', Bar::class, 'SubNs\Bar', false, true, []],
            ['postUpdate', Bar::class, 'SubNs\Bar', false, false, []],
            ['postPersist', Foo::class, 'Foo', true, false, []],
            ['postRemove', Foo::class, 'Foo', true, true, []],
            ['postUpdate', Foo::class, 'Foo', true, false, []],
            ['postPersist', Bar::class, 'SubNs\Bar', true, false, []],
            ['postRemove', Bar::class, 'SubNs\Bar', true, true, []],
            ['postUpdate', Bar::class, 'SubNs\Bar', true, false, []],
            ['postPersist', Foo::class, 'Foo', true, false, ['id' => 123]],
            ['postRemove', Foo::class, 'Foo', true, true, ['id' => 123]],
            ['postUpdate', Foo::class, 'Foo', true, false, ['id' => 123]],
            ['postPersist', Bar::class, 'SubNs\Bar', true, false, ['id' => 123, 'type' => 'foo']],
            ['postRemove', Bar::class, 'SubNs\Bar', true, true, ['id' => 123, 'type' => 'foo']],
            ['postUpdate', Bar::class, 'SubNs\Bar', true, false, ['id' => 123, 'type' => 'foo']],
        ];
    }

    /**
     * @dataProvider getTrackMethods
     *
     * @param string $method
     * @param string $entity_class
     * @param string $entity_name
     * @param bool $track_individually_entity
     * @param bool $remove
     * @param array $ids
     */
    public function testHandleEvent(
        $method,
        $entity_class,
        $entity_name,
        $track_individually_entity,
        $remove,
        array $ids
    ) {
        $entity = new $entity_class;

        $this->keeper
            ->expects($this->at(0))
            ->method('set')
            ->with('AnimeDbCacheTimeKeeperBundle:'.$entity_name, $this->isInstanceOf('DateTime'));

        $this->conf
            ->expects($this->once())
            ->method('getEntityNamespaces')
            ->will($this->returnValue([
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity',
                'AnimeDbCacheTimeKeeperBundle' => 'AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity',
            ]));

        $this->args
            ->expects($this->atLeastOnce())
            ->method('getEntity')
            ->will($this->returnValue($entity));

        if ($track_individually_entity) {
            $meta = $this->getNoConstructorMock(ClassMetadata::class);
            $meta
                ->expects($this->once())
                ->method('getIdentifierValues')
                ->with($entity)
                ->will($this->returnValue($ids));

            $this->em
                ->expects($this->once())
                ->method('getClassMetadata')
                ->with($entity_class)
                ->will($this->returnValue($meta));

            if ($ids) {
                $suffix = Keeper::IDENTIFIER_PREFIX.implode(Keeper::IDENTIFIER_SEPARATOR, $ids);
                if ($remove) {
                    $this->keeper
                        ->expects($this->once())
                        ->method('remove')
                        ->with('AnimeDbCacheTimeKeeperBundle:'.$entity_name.$suffix);
                } else {
                    $this->keeper
                        ->expects($this->at(1))
                        ->method('set')
                        ->with('AnimeDbCacheTimeKeeperBundle:'.$entity_name.$suffix, $this->isInstanceOf('DateTime'));
                }
            }
        } else {
            $this->em
                ->expects($this->never())
                ->method('getClassMetadata');
        }

        $listener = new DoctrineListener($this->keeper, $track_individually_entity);
        call_user_func([$listener, $method], $this->args);
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return [
            ['postPersist'],
            ['postRemove'],
            ['postUpdate'],
        ];
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
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity',
            ]));

        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(new Foo()));

        $listener = new DoctrineListener($this->keeper, false);
        call_user_func([$listener, $method], $this->args);
    }
}
