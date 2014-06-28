<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

/**
 * Dummy driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Dummy implements Driver
{
    /**
     * Now time
     *
     * @var \DateTime
     */
    protected $time;

    /**
     * Construct
     *
     * @param \DateTime $time
     */
    public function __construct(\DateTime $time = null)
    {
        $this->time = $time ?: new \DateTime();
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
        return clone $this->time;
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
        return true;
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
        return clone $this->time;
    }
}