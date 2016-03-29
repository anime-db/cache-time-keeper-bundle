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
 * Multi drivers
 *
 * The driver is a wrapper for multiple drivers. Takes the driver with quick
 * access to the data (stored in memory) and slow (stored on the hard drive),
 * and receives data on the possibility of fast drivers and if not luck reads
 * data from slow.
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Multi extends Base
{
    /**
     * Fast driver
     * 
     * @var DriverInterface
     */
    protected $fast;

    /**
     * Slow driver
     *
     * @var DriverInterface
     */
    protected $slow;

    /**
     * @param DriverInterface $fast
     * @param DriverInterface $slow
     */
    public function __construct(DriverInterface $fast, DriverInterface $slow)
    {
        $this->fast = $fast;
        $this->slow = $slow;
    }

    /**
     * Get time for key
     *
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key)
    {
        if ($time = $this->fast->get($key)) {
            return $time;
        }

        return $this->slow->get($key);
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
        if ($this->fast->set($key, $time)) {
            return $this->slow->set($key, $time);
        }

        return false;
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
        if ($this->fast->remove($key)) {
            return $this->slow->remove($key);
        }

        return false;
    }
}
