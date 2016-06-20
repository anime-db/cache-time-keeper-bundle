<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EtagHasher implements EtagHasherInterface
{
    /**
     * @var string
     */
    const ETAG_SEPARATOR = '|';

    /**
     * @var string
     */
    const DEFAULT_ETAG_ALGORITHM = 'sha256';

    /**
     * @var string
     */
    protected $algorithm = '';

    /**
     * @param string $algorithm
     */
    public function __construct($algorithm = self::DEFAULT_ETAG_ALGORITHM)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return string
     */
    public function hash(Request $request, Response $response)
    {
        return hash(
            $this->algorithm,
            $response->getLastModified()->format(\DateTime::ISO8601).
                self::ETAG_SEPARATOR.
                http_build_query($request->cookies->all())
        );
    }
}
