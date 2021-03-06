<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $original_class_name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getNoConstructorMock($original_class_name)
    {
        return $this
            ->getMockBuilder($original_class_name)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
