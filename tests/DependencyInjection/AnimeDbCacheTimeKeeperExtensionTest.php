<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection\AnimeDbCacheTimeKeeperExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Test DependencyInjection
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCacheTimeKeeperExtensionTest extends TestCase
{
    public function testLoad()
    {
        /* @var $definition \PHPUnit_Framework_MockObject_MockObject|Definition */
        $definition = $this->getMockObject('Symfony\Component\DependencyInjection\Definition');

        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $container
            ->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $di = new AnimeDbCacheTimeKeeperExtension();
        $di->load([], $container);
    }
}
