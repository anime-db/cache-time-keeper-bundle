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

use AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection\AnimeDbCacheTimeKeeperExtension;

/**
 * Test DependencyInjection
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCacheTimeKeeperExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test load
     */
    public function testLoad()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container
            ->expects($this->atLeastOnce())
            ->method('setAlias')
            ->with('cache_time_keeper.driver', 'foo');
        $container
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->with('cache_time_keeper.driver')
            ->will($this->returnValue('foo'));

        $di = new AnimeDbCacheTimeKeeperExtension();
        $di->load([], $container);
    }
}