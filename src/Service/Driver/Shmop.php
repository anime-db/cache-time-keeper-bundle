<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

use AnimeDb\Shmop\FixedBlock as BlockShmop;

class Shmop extends BaseDriver
{
    /**
     * Cache key salt.
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
        $sh = new BlockShmop($this->getIdByKey($key), 10);
        if (!($old_time = $sh->read()) || $old_time < $time->getTimestamp()) {
            $sh->write($time->getTimestamp());
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
        $sh = new BlockShmop($this->getIdByKey($key), 10);

        return $sh->delete();
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function getIdByKey($key)
    {
        return (int) sprintf('%u', crc32($key.$this->salt));
    }
}
