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

use AnimeDb\Shmop\FixedBlock as BlockShmop;

/**
 * Shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Shmop extends BaseDriver
{
    /**
     * Cache key salt
     *
     * @var string
     */
    protected $salt;

    /**
     * @param string $salt
     */
    public function __construct($salt)
    {
        $this->salt = $salt;
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
        $sh = new BlockShmop($this->getIdByKey($key), 10);
        if ($time = $sh->read()) {
            return (new \DateTime())->setTimestamp($time);
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
        $sh = new BlockShmop($this->getIdByKey($key), 10);
        if (!($old_time = $sh->read()) || $old_time < $time->getTimestamp()) {
            $sh->write($time->getTimestamp());
        }

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
        $sh = new BlockShmop($this->getIdByKey($key), 10);

        return $sh->delete();
    }

    /**
     * Get id
     *
     * @param string $key
     *
     * @return integer
     */
    public function getIdByKey($key)
    {
        return (int)sprintf('%u', crc32($key.$this->salt));
    }
}
