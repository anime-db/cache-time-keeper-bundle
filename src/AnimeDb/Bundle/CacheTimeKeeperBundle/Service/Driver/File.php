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
     * Filename
     *
     * @var string
     */
    protected $filename;

    /**
     * List cache times
     *
     * @var array|null
     */
    protected $list = null;

    /**
     * Construct
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
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
        return clone $this->getList()[$key];
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
        $this->getList()[$key] = clone $time;
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
            if (!($value instanceof \DateTime)) {
                $params[$key] = $this->get($value);
            }
        }
        return max($params);
    }

    /**
     * Get list
     *
     * @return array
     */
    protected function & getList()
    {
        if (is_null($this->list)) {
            $this->load();
        }
        return $this->list;
    }

    /**
     * Load list
     */
    protected function load()
    {
        if (file_exists($this->filename)) {
            $this->list = include $this->filename;
            foreach ($this->list as $key => $time) {
                $this->list[$key] = new \DateTime($time);
            }
        } else {
            $this->list = [];
        }
    }

    /**
     * Save list
     */
    protected function save()
    {
        $list = [];
        /* @var $time \DateTime */
        foreach ($this->list as $key => $time) {
            $list[$key] = $time->format(\DateTime::W3C);
        }
        file_put_contents($this->filename, "<?php\nreturn ".var_export($list, true).';');
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->save();
    }
}