<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop;
use AnimeDb\Shmop\FixedBlock as BlockShmop;

class ShmopTest extends BaseDriverTest
{
    /**
     * Cache key salt.
     *
     * @var string
     */
    protected $salt = 'salt';

    /**
     * @var BlockShmop
     */
    protected $sh;

    protected function setUp()
    {
        parent::setUp();
        $this->sh = new BlockShmop($this->getDriver()->getIdByKey(self::TEST_DATA), 10);
        $this->sh->delete();
    }

    protected function tearDown()
    {
        $this->sh->delete();
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
