<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver;

class File extends BaseDriver
{
    /**
     * @var string
     */
    const FILENAME_SUFFIX = '.key';

    /**
     * @var string
     */
    protected $dir;

    /**
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param string $key
     *
     * @return \DateTime|null
     */
    public function get($key)
    {
        $file = $this->getFilename($key);
        if (file_exists($file)) {
            return (new \DateTime())->setTimestamp(filemtime($file));
        }

        return;
    }

    /**
     * @param string $key
     * @param \DateTime $time
     *
     * @return bool
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
     * @param string $key
     *
     * @return bool
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
