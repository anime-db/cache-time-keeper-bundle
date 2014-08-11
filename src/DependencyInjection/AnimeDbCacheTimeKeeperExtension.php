<?php

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AnimeDbCacheTimeKeeperExtension extends Extension
{
    /**
     * {@inheritDoc}
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
