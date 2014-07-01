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
 * Base driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Base implements Driver
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
        if (!$params) {
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