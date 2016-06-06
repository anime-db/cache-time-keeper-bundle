<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection\AnimeDbCacheTimeKeeperExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AnimeDbCacheTimeKeeperExtensionTest extends TestCase
{
    public function testLoad()
    {
        /* @var $definition \PHPUnit_Framework_MockObject_MockObject|Definition */
        $definition = $this->getNoConstructorMock(Definition::class);

        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock(ContainerBuilder::class);
        $container
            ->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $di = new AnimeDbCacheTimeKeeperExtension();
        $di->load([], $container);
    }
}
