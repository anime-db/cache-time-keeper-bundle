<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\ResponseConfigurator;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ResponseConfiguratorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CacheKeyBuilder
     */
    protected $key_builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    protected $request_stack;

    public function setUp()
    {
        $this->key_builder = $this->getNoConstructorMock(CacheKeyBuilder::class);
        $this->request_stack = $this->getMock(RequestStack::class);
    }

    /**
     * @return array
     */
    public function getConfigureParams()
    {
        $not_private_response = new Response();
        $not_private_response->headers->removeCacheControlDirective('private');

        return [
            [
                new Response(),
                new Request(),
                -1,
                [],
                'public',
            ],
            [ // has private headers but not set response as private
                (new Response())->setPublic(),
                new Request([], [], [], [], [], ['HTTP_X_PRIVATE' => '']),
                -1,
                ['X-Private'],
                'public',
            ],
            [ // set response as private
                $not_private_response,
                new Request([], [], [], [], [], ['HTTP_X_PRIVATE' => '']),
                600, // 10 minute
                ['X-Private'],
                'private',
            ],
            [
                (new Response())->setEtag('foo')->setPrivate(),
                new Request(),
                0,
                [],
                'public',
            ],
            [ // test s-maxage
                new Response(),
                new Request(),
                600, // 10 minute
                [],
                'public',
            ],
        ];
    }

    /**
     * @dataProvider getConfigureParams
     *
     * @param Response $response
     * @param Request $request
     * @param int $lifetime
     * @param array $private_headers
     * @param string|null $access
     */
    public function testConfigure(
        Response $response,
        Request $request,
        $lifetime,
        array $private_headers,
        $access
    ) {
        $last_modified = new \DateTime('2016-12-16 16:29:13');

        if ($response->getEtag()) {
            $etag = $response->getEtag();
        } else {
            $etag = md5('bar');
            $this->key_builder
                ->expects($this->once())
                ->method('getEtag')
                ->with($response)
                ->will($this->returnValue($etag));
            $etag = '"'.$etag.'"';
        }

        $this->request_stack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        $expires = null;
        if ($lifetime >= 0) {
            $expires = clone $response->getDate();
            $expires->modify(sprintf('+%s seconds', $lifetime));
        }

        $configurator = new ResponseConfigurator($this->key_builder, $this->request_stack, $private_headers);

        // exec test method
        $configured_response = $configurator->configure($response, $last_modified, $lifetime);

        $this->assertEquals($response, $configured_response);
        $this->assertEquals($last_modified, $response->getLastModified());
        $this->assertEquals($etag, $response->getEtag());
        $this->assertTrue($response->headers->hasCacheControlDirective('must-revalidate'));
        if ($access) {
            $this->assertTrue($response->headers->hasCacheControlDirective($access));
        }

        if ($lifetime >= 0) {
            $this->assertEquals($lifetime, $response->headers->getCacheControlDirective('max-age'));
            $this->assertEquals($expires, $response->getExpires());
            if ($access == 'public') {
                $this->assertEquals($lifetime, $response->headers->getCacheControlDirective('s-maxage'));
            }
        }
    }

    public function testConfigureNoRequest()
    {
        $response = new Response();
        $expected_response = clone $response;

        $this->request_stack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue(null));

        $configurator = new ResponseConfigurator($this->key_builder, $this->request_stack, []);

        $this->assertEquals($expected_response, $configurator->configure($response, new \DateTime(), -1));
    }
}
