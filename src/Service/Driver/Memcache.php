<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

class Memcache extends BaseDriver
{
    /**
     * @var \Memcache
     */
    protected $memcache;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @param \Memcache $memcache
     * @param string $prefix
     */
    public function __construct(\Memcache $memcache, $prefix)
    {
        $this->memcache = $memcache;
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
        if ($time = $this->memcache->get($key)) {
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
        $key = $this->prefix.$key;
        if (!($old_time = $this->memcache->get($key)) || $old_time < $time->getTimestamp()) {
            return $this->memcache->set($key, $time->getTimestamp());
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return $this->memcache->delete($this->prefix.$key);
    }
}
