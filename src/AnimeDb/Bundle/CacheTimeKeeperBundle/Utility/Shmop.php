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
     * Holds the system id for the shared memory block
     *
     * @var integer
     */
    protected $id;

    /**
     * Holds the shared memory block id returned by shmop_open
     *
     * @var integer
     */
    protected $shmid;

    /**
     * Holds the default permission (octal) that will be used in created memory blocks
     *
     * @var integer
     */
    protected $perms = 0644;

    /**
     * Shared memory block instantiation
     *
     * In the constructor we'll check if the block we're going to manipulate
     * already exists or needs to be created. If it exists, let's open it.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        if ($this->exists($this->id)) {
            $this->shmid = shmop_open($this->id, 'w', $this->perms, 10);
        } else {
            $this->shmid = shmop_open($this->id, 'c', $this->perms, 10);
        }
    }

    /**
     * Checks if a shared memory block with the provided id exists or not
     *
     * In order to check for shared memory existance, we have to open it with
     * reading access. If it doesn't exist, warnings will be cast, therefore we
     * suppress those with the @ operator.
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
     * First we check for the block existance, and if it doesn't, we'll create it. Now, if the
     * block already exists, we need to delete it and create it again with a new byte allocation that
     * matches the size of the data that we want to write there. We mark for deletion,  close the semaphore
     * and create it again.
     *
     * @param string $data
     */
    public function write($data)
    {
        if ($this->exists($this->id)) {
            shmop_delete($this->shmid);
            shmop_close($this->shmid);
            $this->shmid = shmop_open($this->id, "c", $this->perms, 10);
            shmop_write($this->shmid, pack('L', $data), 0);
        } else {
            shmop_write($this->shmid, pack('L', $data), 0);
        }
    }

    /**
     * Reads from a shared memory block
     *
     * @return string
     */
    public function read()
    {
        $data = unpack('L', shmop_read($this->shmid, 0, 10));
        return reset($data);
    }

    /**
     * Mark a shared memory block for deletion
     */
    public function delete()
    {
        shmop_delete($this->shmid);
    }

    /**
     * Gets the current shared memory block id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the current shared memory block permissions
     *
     * @return integer
     */
    public function getPermissions()
    {
        return $this->perms;
    }

    /**
     * Sets the default permission (octal) that will be used in created memory blocks
     *
     * @param string $perms
     */
    public function setPermissions($perms)
    {
        $this->perms = $perms;
    }

    /**
     * Closes the shared memory block and stops manipulation
     */
    public function __destruct()
    {
        shmop_close($this->shmid);
    }
}