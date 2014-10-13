<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeper
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Keeper
{
    /**
     * Key for last update of the project
     *
     * @var string
     */
    const LAST_UPDATE_KEY = 'last-update';

    /**
     * Driver
     *
     * @var \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface
     */
    protected $driver;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get time for key
     *
     * @param string $key
     *
     * @return \DateTime
     */
    public function get($key)
    {
        if (!($time = $this->driver->get($key))) {
            if ($key == self::LAST_UPDATE_KEY) {
                $time = new \DateTime();
                $this->driver->set($key, $time);
            } else {
                $time = $this->get(self::LAST_UPDATE_KEY);
            }
        }

        return $time;
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
        return $this->driver->set($key, $time);
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
        return $this->driver->remove($key);
    }

    /**
     * Get a list of keys or dates and chooses the max date
     *
     * @param mixed $params
     *
     * @return \DateTime
     */
    public function getMax($params = [])
    {
        $params = (array)$params;
        // always check the date of the last update of the project
        if (!in_array(self::LAST_UPDATE_KEY, $params)) {
            $params[] = self::LAST_UPDATE_KEY;
        }
        if (!($time = $this->driver->getMax($params))) {
            $time = new \DateTime();
            $this->driver->set(self::LAST_UPDATE_KEY, $time);
        }
        return $time;
    }

    /**
     * Get cache response
     *
     * Set $lifetime as < 0 for not set max-age
     *
     * @param mixed $params
     * @param integer $lifetime
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse($params = [], $lifetime = -1, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if ($lifetime > 0) {
            $response
                ->setMaxAge($lifetime)
                ->setSharedMaxAge($lifetime)
                ->setExpires((new \DateTime())->modify('+'.$lifetime.' seconds'));
        }

        return $response
            ->setPublic()
            ->setLastModified($this->getMax($params));
    }
}
