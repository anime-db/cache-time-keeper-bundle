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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * DI Configuration
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('anime_db_cache_time_keeper');

        /**
         * Example config:
         *
         * anime_db_cache_time_keeper:
         *     use_driver: multi
         *     drivers:
         *         multi:
         *             fast: shmop
         *             slow: file
         *         shmop:
         *             salt: '%secret%'
         *         file:
         *             path: '%kernel.root_dir%/cache/cache-time-keeper/'
         */
        $rootNode
            ->children()
                ->scalarNode('use_driver')->end()
                ->arrayNode('drivers')
                    ->children()
                        ->arrayNode('multi')
                            ->children()
                                ->scalarNode('fast')->end()
                                ->scalarNode('slow')->end()
                            ->end()
                        ->end() // multi
                        ->arrayNode('shmop')
                            ->children()
                                ->scalarNode('salt')->end()
                            ->end()
                        ->end() // shmop
                        ->arrayNode('file')
                            ->children()
                                ->scalarNode('path')->end()
                            ->end()
                        ->end() // file
                    ->end()
                ->end() // drivers
            ->end()
        ;

        return $treeBuilder;
    }
}
