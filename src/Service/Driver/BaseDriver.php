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

/**
 * Base driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class BaseDriver implements DriverInterface
{
    /**
     * Get a list of keys or dates and chooses the max date
     *
     * @param array $params
     *
     * @return \DateTime
     */
    public function getMax(array $params)
    {
        if (empty($params)) {
            throw new \InvalidArgumentException('Unknown key list');
        }

        foreach ($params as $key => $value) {
            if (!($value instanceof \DateTime)) {
                $params[$key] = $this->get($value);
            }
        }

        return max($params);
    }
}
