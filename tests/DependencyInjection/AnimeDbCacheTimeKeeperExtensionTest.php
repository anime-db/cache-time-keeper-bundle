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

class AnimeDbCacheTimeKeeperExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var AnimeDbCacheTimeKeeperExtension
     */
    protected $di;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->di = new AnimeDbCacheTimeKeeperExtension();
    }

    public function testLoadBackwardCompatibility()
    {
        $this->container->setParameter('cache_time_keeper.driver', 'custom.driver');
        $this->container->setParameter('cache_time_keeper.driver.multi.fast', 'custom.driver.fast');
        $this->container->setParameter('cache_time_keeper.driver.multi.slow', 'custom.driver.slow');

        $this->di->load([], $this->container); // test

        // configure drivers
        $this->assertEquals(
            ['%secret%'],
            $this->container->getDefinition('cache_time_keeper.driver.shmop')->getArguments()
        );
        $this->assertEquals(
            ['%kernel.root_dir%/cache/cache-time-keeper/'],
            $this->container->getDefinition('cache_time_keeper.driver.file')->getArguments()
        );
        $this->assertEquals(
            'cache_time_keeper_',
            $this->container->getDefinition('cache_time_keeper.driver.memcache')->getArgument(1)
        );

        // configure memcache
        $this->assertEquals(
            ['cache_time_keeper'],
            $this->container->getDefinition('cache_time_keeper.memcache')->getArguments()
        );
        $this->assertEquals([], $this->container->getDefinition('cache_time_keeper.memcache')->getMethodCalls());

        // service aliases
        $this->assertEquals('custom.driver', (string) $this->container->getAlias('cache_time_keeper.driver'));
        $this->assertEquals(
            'custom.driver.fast',
            (string) $this->container->getAlias('cache_time_keeper.driver.multi.fast')
        );
        $this->assertEquals(
            'custom.driver.slow',
            (string) $this->container->getAlias('cache_time_keeper.driver.multi.slow')
        );
    }

    public function testLoad()
    {
        $config = [
            'anime_db_cache_time_keeper' => [
                'use_driver' => 'file',
                'drivers' => [
                    'multi' => [
                        'fast' => 'shmop',
                        'slow' => 'file',
                    ],
                    'shmop' => [
                        'salt' => 'foo',
                    ],
                    'file' => [
                        'path' => 'cache/cache_time_keeper',
                    ],
                    'memcache' => [
                        'prefix' => 'ctk_',
                        'persistent_id' => 'ctk',
                        'hosts' => [
                            [
                                'host' => '192.168.0.2',
                                'port' => 11211,
                                'weight' => 100,
                            ],
                            [
                                'host' => '192.168.0.3',
                                'port' => 11211,
                                'weight' => 200,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->di->load($config, $this->container); // test

        // configure drivers
        $this->assertEquals(
            ['foo'],
            $this->container->getDefinition('cache_time_keeper.driver.shmop')->getArguments()
        );
        $this->assertEquals(
            ['cache/cache_time_keeper'],
            $this->container->getDefinition('cache_time_keeper.driver.file')->getArguments()
        );
        $this->assertEquals(
            'ctk_',
            $this->container->getDefinition('cache_time_keeper.driver.memcache')->getArgument(1)
        );

        // configure memcache
        $this->assertEquals(
            ['ctk'],
            $this->container->getDefinition('cache_time_keeper.memcache')->getArguments()
        );
        $this->assertEquals(
            [
                [
                    'addServer',
                    $config['anime_db_cache_time_keeper']['drivers']['memcache']['hosts'][0],
                ],
                [
                    'addServer',
                    $config['anime_db_cache_time_keeper']['drivers']['memcache']['hosts'][1],
                ],
            ],
            $this->container->getDefinition('cache_time_keeper.memcache')->getMethodCalls()
        );

        // service aliases
        $this->assertEquals(
            'cache_time_keeper.driver.file',
            (string) $this->container->getAlias('cache_time_keeper.driver')
        );
        $this->assertEquals(
            'cache_time_keeper.driver.shmop',
            (string) $this->container->getAlias('cache_time_keeper.driver.multi.fast')
        );
        $this->assertEquals(
            'cache_time_keeper.driver.file',
            (string) $this->container->getAlias('cache_time_keeper.driver.multi.slow')
        );
    }
}
