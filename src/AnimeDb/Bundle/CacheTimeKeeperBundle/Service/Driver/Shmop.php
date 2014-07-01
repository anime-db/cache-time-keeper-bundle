<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Base;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Utility\Shmop as ShmopUtility;

/**
 * Shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Shmop extends Base
{
    /**
     * Get time for key
     *
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key)
    {
        $sh = new ShmopUtility(self::getIdBykey($key));
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
        $sh = new ShmopUtility(self::getIdBykey($key));
        if (!($old_time = $sh->read()) || $old_time < $time->getTimestamp()) {
            $sh->write($time->getTimestamp());
        }
        return true;
    }

    /**
     * Get id
     *
     * @param string $key
     *
     * @return integer
     */
    public static function getIdBykey($key)
    {
        return (int)sprintf('%u', crc32($key));
    }
}