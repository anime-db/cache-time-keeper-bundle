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
        $config = $this->processConfiguration(new Configuration(), $configs);

        // for backward compatibility merge config from global parameters
        $config = $this->mergeBackwardCompatibilityConfig($config, $container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        // merge default config
        $config = $this->mergeConfig($config, $container);

        $container->getDefinition('cache_time_keeper.driver.shmop')
            ->setArguments([$config['drivers']['shmop']['salt']]);
        $container->getDefinition('cache_time_keeper.driver.file')
            ->setArguments([$config['drivers']['file']['path']]);

        $container->setAlias(
            'cache_time_keeper.driver',
            $this->getRealServiceName($config['use_driver'])
        );

        $container->setAlias(
            'cache_time_keeper.driver.multi.fast',
            $this->getRealServiceName($config['drivers']['multi']['fast'])
        );
        $container->setAlias(
            'cache_time_keeper.driver.multi.slow',
            $this->getRealServiceName($config['drivers']['multi']['slow'])
        );
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getRealServiceName($name)
    {
        if (strpos($name, '.') === false) {
            return 'cache_time_keeper.driver.'.$name;
        }

        return $name;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function mergeBackwardCompatibilityConfig(array $config, ContainerBuilder $container)
    {
        $default_config = [];

        if ($container->hasParameter('cache_time_keeper.driver')) {
            $default_config['use_driver'] = $container->getParameter('cache_time_keeper.driver');
        }

        if ($container->hasParameter('cache_time_keeper.driver.multi.fast')) {
            $default_config['drivers']['multi']['fast'] = $container
                ->getParameter('cache_time_keeper.driver.multi.fast');
        }

        if ($container->hasParameter('cache_time_keeper.driver.multi.slow')) {
            $default_config['drivers']['multi']['slow'] = $container
                ->getParameter('cache_time_keeper.driver.multi.slow');
        }

        return array_merge($default_config, $config);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function mergeConfig(array $config, ContainerBuilder $container)
    {
        return array_merge([
            'use_driver' => $container->getParameter('cache_time_keeper.driver'),
            'drivers' => [
                'multi' => [
                    'fast' => $container->getParameter('cache_time_keeper.driver.multi.fast'),
                    'slow' => $container->getParameter('cache_time_keeper.driver.multi.slow')
                ],
                'shmop' => [
                    'salt' => $container->getParameter('cache_time_keeper.driver.shmop.salt')
                ],
                'file' => [
                    'path' => $container->getParameter('cache_time_keeper.driver.file.path')
                ]
            ]
        ], $config);
    }
}
