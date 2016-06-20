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
use Symfony\Component\HttpFoundation\Response;

class EtagHasherTest extends TestCase
{
    /**
     * @return array
     */
    public function getHashParams()
    {
        return [
            ['sha256', []],
            ['md5', ['foo' => 'bar']],
        ];
    }

    /**
     * @dataProvider getHashParams
     *
     * @param string $algorithm
     * @param array $cookies
     */
    public function testHash($algorithm, array $cookies)
    {
        $last_modified = new \DateTime('-1 day');

        $request = new Request([], [], [], $cookies, [], [], []);
        $response = (new Response())->setLastModified($last_modified);

        $hasher = new EtagHasher($algorithm);

        $etag = hash(
            $algorithm,
            $response->getLastModified()->format(\DateTime::ISO8601).
                EtagHasher::ETAG_SEPARATOR.
                http_build_query($cookies)
        );

        $this->assertEquals($etag, $hasher->hash($request, $response));
    }
}
