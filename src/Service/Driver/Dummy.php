<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

class Dummy extends BaseDriver
{
    /**
     * @var array
     */
    protected $times = [];

    /**
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key)
    {
        if (isset($this->times[$key])) {
            return clone $this->times[$key];
        }

        return;
    }

    /**
     * @param string $key
     * @param \DateTime $time
     *
     * @return bool
     */
    public function set($key, \DateTime $time)
    {
        $this->times[$key] = clone $time;

        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        if (isset($this->times[$key])) {
            unset($this->times[$key]);

            return true;
        }

        return false;
    }
}
