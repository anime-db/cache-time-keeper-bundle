<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Utility;

/**
 * Shmop utility
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Utility
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Shmop
{
    /**
     * Default permission (octal) that will be used in created memory blocks
     *
     * @var integer
     */
    const DEFAULT_PERMISSION = 0644;

    /**
     * Shared memory block id returned by shmop_open
     *
     * @var integer
     */
    protected $shmid;

    /**
     * Shared memory block instantiation
     *
     * In the constructor we'll check if the block we're going to manipulate
     * already exists or needs to be created. If it exists, let's open it.
     *
     * @param string $id
     * @param integer $size
     * @param integer $perms
     */
    public function __construct($id, $size, $perms = self::DEFAULT_PERMISSION)
    {
        $this->id = $id;
        if ($this->exists($this->id)) {
            $this->shmid = shmop_open($this->id, 'w', $perms, $size);
        } else {
            $this->shmid = shmop_open($this->id, 'c', $perms, $size);
        }
    }

    /**
     * Checks if a shared memory block with the provided id exists or not
     *
     * @param string $id
     *
     * @return boolean
     */
    public function exists($id)
    {
        return @shmop_open($id, 'a', 0, 0);
    }

    /**
     * Writes on a shared memory block
     *
     * @param string $data
     */
    public function write($data)
    {
        shmop_write($this->shmid, $data, 0);
    }

    /**
     * Reads from a shared memory block
     *
     * @return string
     */
    public function read()
    {
        return trim(shmop_read($this->shmid, 0, shmop_size($this->shmid)));
    }

    /**
     * Mark a shared memory block for deletion
     */
    public function delete()
    {
        shmop_delete($this->shmid);
    }

    /**
     * Closes the shared memory block and stops manipulation
     */
    public function __destruct()
    {
        shmop_close($this->shmid);
    }
}