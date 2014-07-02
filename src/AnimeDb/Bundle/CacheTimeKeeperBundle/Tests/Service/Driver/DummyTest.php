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
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy;

/**
 * Test dummy driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class DummyTest extends DriverTest
{
    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\DriverTest::getDriver()
     */
    protected function getDriver()
    {
        return new Dummy();
    }

    /**
     * Test sync list times
     */
    public function testSync()
    {
        // in dummy driver a sync does not work
    }
}