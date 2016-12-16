<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class EtagHasher implements EtagHasherInterface
{
    /**
     * @var string
     */
    const ETAG_SEPARATOR = '|';

    /**
     * @var RequestStack
     */
    protected $request_stack;

    /**
     * @var string
     */
    protected $algorithm = '';

    /**
     * @param RequestStack $request_stack
     * @param string $algorithm
     */
    public function __construct(RequestStack $request_stack, $algorithm)
    {
        $this->request_stack = $request_stack;
        $this->algorithm = $algorithm;
    }

    /**
     * @param Response $response
     *
     * @return string
     */
    public function hash(Response $response)
    {
        $params = [
            $response->getLastModified()->format(\DateTime::ISO8601),
        ];

        // add cookies to ETag
        if ($this->request_stack->getMasterRequest()) {
            $params[] = http_build_query($this->request_stack->getMasterRequest()->cookies->all());
        }

        return hash($this->algorithm, implode(self::ETAG_SEPARATOR, $params));
    }
}
