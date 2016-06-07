<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $shmop_salt = '';

    /**
     * @var string
     */
    protected $file_path = '';

    /**
     * @param string $shmop_salt
     * @param string $file_path
     */
    public function __construct($shmop_salt, $file_path)
    {
        $this->shmop_salt = $shmop_salt;
        $this->file_path = $file_path;
    }

    /**
     * Config tree builder.
     *
     * Example config:
     *
     * anime_db_cache_time_keeper:
     *     use_driver: file
     *     track:
     *         clear_cache: true
     *     drivers:
     *         multi:
     *             fast: shmop
     *             slow: file
     *         shmop:
     *             salt: '%secret%'
     *         file:
     *             path: '%kernel.root_dir%/cache/cache-time-keeper/'
     *         memcache:
     *             prefix: 'cache_time_keeper_'
     *             persistent_id: 'cache_time_keeper'
     *             hosts:
     *                 - {host: 'localhost', port: 11211, weight: 100}
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $tree_builder = new TreeBuilder();
        $tree_builder
            ->root('anime_db_cache_time_keeper')
                ->children()
                    ->scalarNode('use_driver')
                        ->cannotBeEmpty()
                        ->defaultValue('file')
                    ->end()
                    ->arrayNode('track')
                        ->children()
                            ->booleanNode('clear_cache')
                                ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('drivers')
                        ->append($this->getDriverFile())
                        ->append($this->getDriverMemcache())
                        ->append($this->getDriverMulti())
                        ->append($this->getDriverShmop())
                    ->end()
                ->end();

        return $tree_builder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getDriverMulti()
    {
        $tree_builder = new TreeBuilder();

        return $tree_builder
            ->root('multi')
                ->children()
                    ->scalarNode('fast')
                        ->cannotBeEmpty()
                        ->defaultValue('shmop')
                    ->end()
                    ->scalarNode('slow')
                        ->cannotBeEmpty()
                        ->defaultValue('file')
                    ->end()
                ->end();
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getDriverShmop()
    {
        $tree_builder = new TreeBuilder();

        return $tree_builder
            ->root('shmop')
                ->children()
                    ->scalarNode('salt')
                        ->cannotBeEmpty()
                        ->defaultValue($this->shmop_salt)
                    ->end()
                ->end();
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getDriverFile()
    {
        $tree_builder = new TreeBuilder();

        return $tree_builder
            ->root('file')
                ->children()
                    ->scalarNode('path')
                        ->cannotBeEmpty()
                        ->defaultValue($this->file_path)
                    ->end()
                ->end();
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getDriverMemcache()
    {
        $tree_builder = new TreeBuilder();

        return $tree_builder
            ->root('memcache')
                ->children()
                    ->scalarNode('prefix')
                        ->defaultValue('cache_time_keeper_')
                    ->end()
                    ->scalarNode('persistent_id')
                        ->defaultValue('cache_time_keeper')
                        ->info(
                            'Specify to enable persistent connections. '.
                            'All clients with the same ID share connections.'
                        )
                    ->end()
                    ->arrayNode('hosts')
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('host')
                                    ->cannotBeEmpty()
                                    ->defaultValue('localhost')
                                ->end()
                                ->scalarNode('port')
                                    ->cannotBeEmpty()
                                    ->defaultValue(11211)
                                    ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_numeric($v);
                                    })
                                        ->thenInvalid('Host port must be numeric')
                                    ->end()
                                ->end()
                                ->scalarNode('weight')
                                    ->defaultValue(0)
                                    ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_numeric($v);
                                    })
                                        ->thenInvalid('Host weight must be numeric')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
    }
}
