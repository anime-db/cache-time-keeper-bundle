<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Base;

/**
 * Dummy driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Dummy extends Base
{
    /**
     * List times
     *
     * @var array
     */
    protected $list = [];

    /**
     * Get time for key
     *
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key)
    {
        if (isset($this->list[$key])) {
            return clone $this->list[$key];
        }
        return null;
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
        $this->list[$key] = clone $time;
        return true;
    }

    /**
     * Remove time for key
     *
     * @param string $key
     *
     * @return boolean
     */
    public function remove($key)
    {
        if (isset($this->list[$key])) {
            unset($this->list[$key]);
            return true;
        }
        return false;
    }
}
