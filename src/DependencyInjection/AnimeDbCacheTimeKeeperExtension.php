<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * DependencyInjection
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCacheTimeKeeperExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        $container->setAlias(
            'cache_time_keeper.driver',
            $container->getParameter('cache_time_keeper.driver')
        );
        $container->setAlias(
            'cache_time_keeper.driver.multi.fast',
            $container->getParameter('cache_time_keeper.driver.multi.fast')
        );
        $container->setAlias(
            'cache_time_keeper.driver.multi.slow',
            $container->getParameter('cache_time_keeper.driver.multi.slow')
        );
    }
}
