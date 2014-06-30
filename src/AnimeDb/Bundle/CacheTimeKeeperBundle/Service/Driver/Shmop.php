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
use Simple\SHM\Block;

/**
 * Shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Shmop implements Driver
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
        $block = new Block($this->getId($key));
        if ($time = $block->read()) {
            return new \DateTime($time);
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
        $block = new Block($this->getId($key));
        if ($old_time = $block->read() && strtotime($old_time) < $time->getTimestamp()) {
            $block->write($time->format('Y-m-d H:i:s'));
        }
        return true;
    }

    /**
     * Get a list of keys or dates and chooses the max date
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
     * Save list times if need
     *
     * @return boolean
     */
    public function save()
    {
        return true;
    }

    /**
     * Get id
     *
     * @param string $key
     *
     * @return integer
     */
    protected function getId($key)
    {
        return (int)sprintf('%u', crc32($key));
    }
}