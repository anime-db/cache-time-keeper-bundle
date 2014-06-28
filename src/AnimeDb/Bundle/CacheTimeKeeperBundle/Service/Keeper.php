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
 * Keeper
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Keeper implements Driver
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
     * Get time for key
     *
     * @param string $key
     *
     * @return \DateTime
     */
    public function get($key)
    {
        return $this->driver->get($key);
    }

    /**
     * Set time for key
     *
     * @param string $key
     * @param \DateTime $time
     *
     * @return boolean
     */
    public function set($key, \DateTime $time)
    {
        return $this->driver->set($key, $time);
    }

    /**
     * Get a list of keys or dates and chooses the max date
     *
     * @param array $params
     *
     * @return \DateTime
     */
    public function getMax(array $params)
    {
        return $this->driver->getMax($params);
    }
}