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

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

/**
 * File driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class File implements Driver
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
     * Get a list of keys or dates and chooses the max date
     *
     * @throws \InvalidArgumentException
     *
     * @param array $params
     *
     * @return \DateTime
     */
    public function getMax(array $params)
    {
        if (!$params) {
            throw new \InvalidArgumentException('Unknown key list');
        }
        foreach ($params as $key => $value) {
            if (is_scalar($value)) {
                $params[$key] = $this->get($value);
            } elseif (!($value instanceof \DateTime)) {
                throw new \InvalidArgumentException('No supported ('.gettype($value).')');
            }
        }
        return max($params);
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
        return $this->dir.'/'.md5($key).self::FILENAME_SUFFIX;
    }
}