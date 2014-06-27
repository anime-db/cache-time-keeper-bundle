<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

/**
 * Manager
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Manager
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
}