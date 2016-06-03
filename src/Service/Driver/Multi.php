<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

/**
 * Multi drivers.
 *
 * The driver is a wrapper for multiple drivers. Takes the driver with quick
 * access to the data (stored in memory) and slow (stored on the hard drive),
 * and receives data on the possibility of fast drivers and if not luck reads
 * data from slow.
 */
class Multi extends BaseDriver
{
    /**
     * @var DriverInterface
     */
    protected $fast;

    /**
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
     * @param string $key
     * @param \DateTime $time
     *
     * @return bool
     */
    public function set($key, \DateTime $time)
    {
        if ($this->fast->set($key, $time)) {
            return $this->slow->set($key, $time);
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        if ($this->fast->remove($key)) {
            return $this->slow->remove($key);
        }

        return false;
    }
}
