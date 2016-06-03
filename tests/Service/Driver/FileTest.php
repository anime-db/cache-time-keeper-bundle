<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\Driver;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\File;

class FileTestBase extends BaseDriverTest
{
    /**
     * Metadata dir
     *
     * @var string
     */
    protected $dir;

    protected function setUp()
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir().'/unit-test.meta/';
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755);
        }
    }

    public function tearDown()
    {
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
     * Test make dir for story cache if not exists.
     */
    public function testMakeDir()
    {
        $obj = new File($this->dir.'test/');
        $this->assertTrue($obj->set(self::DATE_KEY, $this->time));
        $this->assertTrue(is_dir($this->dir.'test/'));
        $this->assertTrue($obj->remove(self::DATE_KEY));
        rmdir($this->dir.'test/');
    }

    /**
     * @return File
     */
    protected function getDriver()
    {
        return new File($this->dir);
    }
}
