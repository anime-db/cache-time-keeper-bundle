<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

interface DriverInterface
{
    /**
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key);

    /**
     * @param string $key
     * @param \DateTime $time
     *
     * @return bool
     */
    public function set($key, \DateTime $time);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key);

    /**
     * Get a list of keys or dates and chooses the max date.
     *
     * @param array $params
     *
     * @return \DateTime
     */
    public function getMax(array $params);
}
