<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\DriverTest;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop;
use AnimeDb\Shmop\FixedBlock as BlockShmop;

/**
 * Test shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class ShmopTest extends DriverTest
{
    /**
     * Cache key salt
     *
     * @var string
     */
    protected $salt = 'salt';

    protected function setUp()
    {
        parent::setUp();
        $sh = new BlockShmop($this->getDriver()->getIdByKey('foo'), 3);
        $sh->delete();
    }

    public function testRemoveFail()
    {
        // empty memory block can always remove
    }

    /**
     * @return Shmop
     */
    protected function getDriver()
    {
        return new Shmop($this->salt);
    }
}
