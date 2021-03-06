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

        // test params from config
        $this->assertEquals('%secret%', $this->container->getParameter('cache_time_keeper.driver.shmop.salt'));
        $this->assertEquals(
            '%kernel.root_dir%/cache/cache-time-keeper/',
            $this->container->getParameter('cache_time_keeper.driver.file.path')
        );
        $this->assertEquals(
            'cache_time_keeper_',
            $this->container->getParameter('cache_time_keeper.driver.memcache.prefix')
        );
        $this->assertTrue($this->container->getParameter('cache_time_keeper.track.clear_cache'));
        $this->assertFalse($this->container->getParameter('cache_time_keeper.track.individually'));
        $this->assertTrue($this->container->getParameter('cache_time_keeper.enable'));
        $this->assertEquals('sha256', $this->container->getParameter('cache_time_keeper.etag.algorithm'));
        $this->assertEquals(['Authorization', 'Cookie'], $this->container->getParameter('cache_time_keeper.private_headers'));

        // configure memcache
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
        $this->assertEquals(
            'cache_time_keeper.cache_key_builder.default_etag_hasher',
            (string) $this->container->getAlias('cache_time_keeper.cache_key_builder.etag_hasher')
        );
        $this->assertEquals(
            'cache_time_keeper.cache_key_builder.default_etag_hasher',
            (string) $this->container->getAlias('cache_time_keeper.cache_key_builder.etag_hasher')
        );
    }

    public function testLoad()
    {
        $config = [
            'anime_db_cache_time_keeper' => [
                'enable' => false,
                'use_driver' => 'file',
                'private_headers' => ['X-Custom-Header'],
                'etag_hasher' => [
                    'driver' => 'custom_etag_hasher',
                    'algorithm' => 'md5',
                ],
                'track' => [
                    'clear_cache' => false,
                    'individually_entity' => true,
                ],
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

        // test params from config
        $this->assertEquals('foo', $this->container->getParameter('cache_time_keeper.driver.shmop.salt'));
        $this->assertEquals(
            'cache/cache_time_keeper',
            $this->container->getParameter('cache_time_keeper.driver.file.path')
        );
        $this->assertEquals('ctk_', $this->container->getParameter('cache_time_keeper.driver.memcache.prefix'));
        $this->assertFalse($this->container->getParameter('cache_time_keeper.track.clear_cache'));
        $this->assertTrue($this->container->getParameter('cache_time_keeper.track.individually'));
        $this->assertFalse($this->container->getParameter('cache_time_keeper.enable'));
        $this->assertEquals('md5', $this->container->getParameter('cache_time_keeper.etag.algorithm'));
        $this->assertEquals(['X-Custom-Header'], $this->container->getParameter('cache_time_keeper.private_headers'));

        // configure memcache
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
        $this->assertEquals(
            'custom_etag_hasher',
            (string) $this->container->getAlias('cache_time_keeper.cache_key_builder.etag_hasher')
        );
    }
}
