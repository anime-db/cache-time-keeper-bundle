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
use AnimeDb\Shmop\FixedBlock as BlockShmop;

/**
 * Shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Shmop extends Base
{
    /**
     * Cache key salt
     *
     * @var string
     */
    protected $salt;

    /**
     * Construct
     *
     * @param string $salt
     */
    public function __construct($salt)
    {
        // can't test it
        // @codeCoverageIgnoreStart
        if (!extension_loaded('shmop')) {
            throw new \RuntimeException('Extension "shmop" is not loaded');
        }
        // @codeCoverageIgnoreEnd
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
        $sh = new BlockShmop($this->getIdBykey($key), 10);
        if ($time = $sh->read()) {
            return new \DateTime(date('Y-m-d H:i:s', $time));
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
        $sh = new BlockShmop($this->getIdBykey($key), 10);
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
        $sh = new BlockShmop($this->getIdBykey($key), 10);
        return $sh->delete();
    }

    /**
     * Get id
     *
     * @param string $key
     *
     * @return integer
     */
    public function getIdBykey($key)
    {
        return (int)sprintf('%u', crc32($key.$this->salt));
    }
}
