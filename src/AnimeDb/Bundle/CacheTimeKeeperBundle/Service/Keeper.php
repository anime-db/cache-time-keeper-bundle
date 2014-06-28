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
class Keeper
{
    /**
     * Key for last update of the project
     *
     * @var string
     */
    const LAST_UPDATE_KEY = 'last-update';

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
        if (!($time = $this->driver->get($key))) {
            if ($key == self::LAST_UPDATE_KEY) {
                $time = new \DateTime();
                $this->driver->set($key, $time);
            } else {
                $time = $this->get(self::LAST_UPDATE_KEY);
            }
        }

        return $time;
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
        // always check the date of the last update of the project
        if (!in_array(self::LAST_UPDATE_KEY, $params)) {
            $params[] = self::LAST_UPDATE_KEY;
        }
        return $this->driver->getMax($params);
    }
}