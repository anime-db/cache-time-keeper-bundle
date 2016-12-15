<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service\CacheKeyBuilder;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder\EtagHasher;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class EtagHasherTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    protected $request_stack;

    public function setUp()
    {
        $this->request_stack = $this->getMock(RequestStack::class);
    }

    /**
     * @return array
     */
    public function getHashParams()
    {
        return [
            ['sha256', new Request()],
            ['md5', new Request([], [], [], ['foo' => 'bar'], [], [], [])],
            ['sha1', null],
        ];
    }

    /**
     * @dataProvider getHashParams
     *
     * @param string $algorithm
     * @param Request|null $request
     */
    public function testHash($algorithm, Request $request = null)
    {
        $last_modified = new \DateTime('-1 day');

        $response = (new Response())->setLastModified($last_modified);

        $this->request_stack
            ->expects($this->atLeastOnce())
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        $hasher = new EtagHasher($this->request_stack, $algorithm);

        $suffix = '';
        if ($request) {
            $suffix = EtagHasher::ETAG_SEPARATOR.http_build_query($request->cookies->all());
        }

        $etag = hash($algorithm, $response->getLastModified()->format(\DateTime::ISO8601).$suffix);

        $this->assertEquals($etag, $hasher->hash($response));
    }
}
