<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\Dummy;

class DummyTest extends BaseDriverTest
{
    /**
     * @return Dummy
     */
    protected function getDriver()
    {
        return new Dummy();
    }

    public function testSync()
    {
        // in dummy driver a sync does not work
    }
}
