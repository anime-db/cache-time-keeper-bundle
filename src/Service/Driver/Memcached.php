<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

class Memcached extends BaseDriver
{
    /**
     * @var \Memcached
     */
    protected $memcached;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @param \Memcached $memcached
     * @param string $prefix
     */
    public function __construct(\Memcached $memcached, $prefix)
    {
        $this->memcached = $memcached;
        $this->prefix = $prefix;
    }

    /**
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key)
    {
        $key = $this->prefix.$key;
        if ($time = $this->memcached->get($key)) {
            return (new \DateTime())->setTimestamp($time);
        }

        return null;
    }

    /**
     * @param string $key
     * @param \DateTime $time
     *
     * @return bool
     */
    public function set($key, \DateTime $time)
    {
        return $this->memcached->set($this->prefix.$key, $time->getTimestamp());
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return $this->memcached->delete($this->prefix.$key);
    }
}
