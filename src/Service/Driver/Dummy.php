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

/**
 * Dummy driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Dummy extends BaseDriver
{
    /**
     * List times
     *
     * @var array
     */
    protected $list = [];

    /**
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
