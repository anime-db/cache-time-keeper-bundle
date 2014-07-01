<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\DriverTest;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Shmop;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Utility\Shmop as ShmopUtility;

/**
 * Test shmop driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class ShmopTest extends DriverTest
{
    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function setUp()
    {
        parent::setUp();
        $sh = new ShmopUtility(Shmop::getIdBykey('foo'));
        $sh->delete();
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\DriverTest::getDriver()
     */
    protected function getDriver()
    {
        return new Shmop();
    }
}