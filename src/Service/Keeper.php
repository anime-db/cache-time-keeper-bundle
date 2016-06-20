<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Exception\NotModifiedException;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Keeper
{
    /**
     * Key for last update of the project.
     *
     * @var string
     */
    const LAST_UPDATE_KEY = 'last-update';

    /**
     * @var string
     */
    const IDENTIFIER_SEPARATOR = CacheKeyBuilder::IDENTIFIER_SEPARATOR;

    /**
     * @var string
     */
    const IDENTIFIER_PREFIX = CacheKeyBuilder::IDENTIFIER_PREFIX;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var ResponseConfigurator
     */
    protected $configurator;

    /**
     * @var bool
     */
    protected $enable = true;

    /**
     * @param DriverInterface $driver
     * @param ResponseConfigurator $configurator
     * @param bool $enable
     */
    public function __construct(DriverInterface $driver, ResponseConfigurator $configurator, $enable)
    {
        $this->driver = $driver;
        $this->configurator = $configurator;
        $this->enable = $enable;
    }

    /**
     * Get time for key.
     *
     * @param string $key
     *
     * @return \DateTime
     */
    public function get($key)
    {
        if (!($time = $this->driver->get($key))) {
            if ($key == self::LAST_UPDATE_KEY) {
                $time = $this->reset();
            } else {
                $time = $this->get(self::LAST_UPDATE_KEY);
            }
        }

        return $time;
    }

    /**
     * Set time for key.
     *
     * @param string $key
     * @param \DateTime $time
     *
     * @return bool
     */
    public function set($key, \DateTime $time)
    {
        return $this->driver->set($key, $time);
    }

    /**
     * Remove time for key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return $this->driver->remove($key);
    }

    /**
     * Get a list of keys or dates and chooses the max date.
     *
     * @param mixed $params
     *
     * @return \DateTime
     */
    public function getMax($params = [])
    {
        if (!$this->enable) {
            return new \DateTime();
        }

        $params = (array) $params;
        // always check the date of the last update of the project
        if (!in_array(self::LAST_UPDATE_KEY, $params)) {
            $params[] = self::LAST_UPDATE_KEY;
        }

        if (!($time = $this->driver->getMax($params))) {
            $time = $this->reset();
        }

        return $time;
    }

    /**
     * Get cache response.
     *
     * Set $lifetime as < 0 for not set max-age
     *
     * @param mixed $params
     * @param int $lifetime
     * @param Response|null $response
     *
     * @return Response
     */
    public function getResponse($params = [], $lifetime = -1, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if (!$this->enable) {
            return $response;
        }

        return $this->configurator->configure($response, $this->getMax($params), $lifetime);
    }

    /**
     * Get only modified response.
     *
     * Throw exception if response was not modified for this request
     *
     * Set $lifetime as < 0 for not set max-age
     *
     * @throws NotModifiedException
     *
     * @param Request $request
     * @param mixed $params
     * @param int $lifetime
     * @param Response|null $response
     *
     * @return Response
     */
    public function getModifiedResponse(Request $request, $params = [], $lifetime = -1, Response $response = null)
    {
        $response = $this->getResponse($params, $lifetime, $response);

        if ($response->isNotModified($request)) {
            throw new NotModifiedException($response);
        }

        return $response;
    }

    /**
     * Reset last update date.
     *
     * @return \DateTime
     */
    private function reset()
    {
        $time = new \DateTime();
        $this->driver->set(self::LAST_UPDATE_KEY, $time);

        return $time;
    }
}
