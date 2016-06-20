<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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

        $configuration = new Configuration(
            $container->getParameter('cache_time_keeper.driver.shmop.salt'),
            $container->getParameter('cache_time_keeper.driver.file.path')
        );
        $config = $this->processConfiguration($configuration, $configs);

        $config = $this->mergeBackwardCompatibilityConfig($config, $container);
        $config = $this->mergeDefaultConfig($config, $container);

        // configure drivers
        $container
            ->getDefinition('cache_time_keeper.driver.shmop')
            ->replaceArgument(0, $config['drivers']['shmop']['salt']);
        $container
            ->getDefinition('cache_time_keeper.driver.file')
            ->replaceArgument(0, $config['drivers']['file']['path']);
        $container
            ->getDefinition('cache_time_keeper.driver.memcache')
            ->replaceArgument(1, $config['drivers']['memcache']['prefix']);
        $container
            ->getDefinition('cache_time_keeper.listener.console')
            ->replaceArgument(1, $config['track']['clear_cache']);
        $container
            ->getDefinition('cache_time_keeper.listener.doctrine')
            ->replaceArgument(1, $config['track']['individually_entity']);
        $container
            ->getDefinition('cache_time_keeper')
            ->replaceArgument(2, $config['enable']);
        $container
            ->getDefinition('cache_time_keeper.cache_key_builder.default_etag_hasher')
            ->replaceArgument(0, $config['etag_hasher']['algorithm']);
        $container
            ->getDefinition('cache_time_keeper.response_configurator')
            ->replaceArgument(2, $config['private_headers']);

        // configure memcache
        $memcache = $container
            ->getDefinition('cache_time_keeper.memcache')
            ->replaceArgument(0, $config['drivers']['memcache']['persistent_id']);
        foreach ($config['drivers']['memcache']['hosts'] as $host) {
            $memcache->addMethodCall('addServer', $host);
        }

        // add service aliases
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
        $container->setAlias('cache_time_keeper.cache_key_builder.etag_hasher', $config['etag_hasher']['driver']);
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
        if ($container->hasParameter('cache_time_keeper.driver')) {
            $config['use_driver'] = $container->getParameter('cache_time_keeper.driver');
        }

        foreach (['fast', 'slow'] as $item) {
            if (empty($config['drivers']['multi'][$item]) &&
                $container->hasParameter('cache_time_keeper.driver.multi.'.$item)
            ) {
                $config['drivers']['multi'][$item] = $container->getParameter('cache_time_keeper.driver.multi.'.$item);
            }
        }

        return $config;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function mergeDefaultConfig(array $config, ContainerBuilder $container)
    {
        $config = array_merge([
            'enable' => true,
            'use_driver' => 'file',
            'private_headers' => ['Authorization', 'Cookie'],
            'etag_hasher' => [],
            'track' => [],
            'drivers' => [],
        ], $config);

        $config['etag_hasher'] = array_merge([
            'driver' => 'cache_time_keeper.cache_key_builder.default_etag_hasher',
            'algorithm' => 'sha256',
        ], $config['etag_hasher']);

        $config['track'] = array_merge([
            'clear_cache' => true,
            'individually_entity' => false,
        ], $config['track']);

        $config['drivers'] = array_merge([
            'multi' => [
                'fast' => 'shmop',
                'slow' => 'file',
            ],
            'shmop' => [
                'salt' => $container->getParameter('cache_time_keeper.driver.shmop.salt'),
            ],
            'file' => [
                'path' => $container->getParameter('cache_time_keeper.driver.file.path'),
            ],
            'memcache' => [
                'prefix' => 'cache_time_keeper_',
                'persistent_id' => 'cache_time_keeper',
                'hosts' => [],
            ],
        ], $config['drivers']);

        return $config;
    }
}
