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

/**
 * File driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class File extends Base
{
    /**
     * Filename suffix
     *
     * @var string
     */
    const FILENAME_SUFFIX = '.key';

    /**
     * Dir
     *
     * @var string
     */
    protected $dir;

    /**
     * Construct
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
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
        $file = $this->getFilename($key);
        if (file_exists($file)) {
            return new \DateTime(date('Y-m-d H:i:s', filemtime($file)));
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
        $time = $time->getTimestamp();
        $file = $this->getFilename($key);
        if (!file_exists($file) || $time > filemtime($file)) {
            return touch($file, $time);
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
        $file = $this->getFilename($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    /**
     * Get filename from key
     *
     * @param string $key
     *
     * @return string
     */
    protected function getFilename($key)
    {
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755, true);
        }
        return $this->dir.'/'.md5($key).self::FILENAME_SUFFIX;
    }
}