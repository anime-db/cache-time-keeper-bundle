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
     * List is save
     *
     * @var boolean
     */
    protected $save = false;

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
        $this->load();
        if (isset($this->list[$key])) {
            return clone $this->list[$key];
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
        $this->load();
        $this->list[$key] = clone $time;
        $this->save = false;
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
     * Save list times if need
     *
     * @return boolean
     */
    public function save()
    {
        if ($this->save || is_null($this->list)) {
            return true;
        }
        $result = file_put_contents($this->filename, "<?php\nreturn ".var_export($this->list, true).';');
        $this->save = $result !== false;
        return $result !== false;
    }

    /**
     * Load list
     */
    protected function load()
    {
        if (is_null($this->list)) {
            if (file_exists($this->filename)) {
                $this->list = include $this->filename;
            } else {
                $this->list = [];
            }
            $this->save = true;
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->save();
    }
}