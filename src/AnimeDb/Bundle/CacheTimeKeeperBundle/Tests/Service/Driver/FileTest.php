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
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File;

/**
 * Test file driver
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class FileTest extends DriverTest
{
    /**
     * Metadata dir
     *
     * @var string
     */
    protected $dir;

    /**
     * Construct
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir().'/unit-test.meta/';
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755);
        }
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        parent::tearDown();
        if (is_dir($this->dir)) {
            foreach (scandir($this->dir) as $value) {
                if ($value[0] != '.') {
                    @unlink($this->dir.'/'.$value);
                }
            }
            rmdir($this->dir);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CacheTimeKeeperBundle\Test\Service\DriverTest::getDriver()
     */
    protected function getDriver()
    {
        return new File($this->dir);
    }
}