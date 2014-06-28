<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Test;

use AnimeDb\Bundle\CacheTimeKeeperBundle\AnimeDbCacheTimeKeeperBundle;

/**
 * Test bundle
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Test
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCacheTimeKeeperBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test bundle
     */
    public function testBundle()
    {
        $this->assertInstanceOf('\Symfony\Component\HttpKernel\Bundle\Bundle', new AnimeDbCacheTimeKeeperBundle());
    }
}