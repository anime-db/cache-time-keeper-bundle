<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

/**
 * Request listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Request
{
    /**
     * Driver
     *
     * @var \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
     */
    protected $driver;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * On terminate
     *
     * @param \Symfony\Component\HttpKernel\Event\PostResponseEvent $event
     */
    public function onTerminate()
    {
        $this->driver->save();
    }
}
