<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ResponseConfigurator
{
    /**
     * @var CacheKeyBuilder
     */
    protected $key_builder;

    /**
     * @var RequestStack
     */
    protected $request_stack;

    /**
     * @var array
     */
    protected $private_headers = [];

    /**
     * @param CacheKeyBuilder $key_builder
     * @param RequestStack $request_stack
     * @param array $private_headers
     */
    public function __construct(CacheKeyBuilder $key_builder, RequestStack $request_stack, array $private_headers)
    {
        $this->key_builder = $key_builder;
        $this->request_stack = $request_stack;
        $this->private_headers = $private_headers;
    }

    /**
     * Configure response
     *
     * Set $lifetime as < 0 for not set max-age
     *
     * @param Response $response
     * @param \DateTime $last_modified
     * @param int $lifetime
     *
     * @return Response
     */
    public function configure(Response $response, \DateTime $last_modified, $lifetime)
    {
        $request = $this->request_stack->getMasterRequest();

        if (!($request instanceof Request)) {
            return $response;
        }

        // order is important
        $this
            ->setPrivateCache($response, $request)
            ->setLastModified($response, $last_modified)
            ->setLifetime($response, $lifetime)
            ->setEtag($response, $request);

        return $response;
    }

    /**
     * @param Response $response
     * @param Request $request
     *
     * @return ResponseConfigurator
     */
    protected function setPrivateCache(Response $response, Request $request)
    {
        if (!$response->headers->hasCacheControlDirective('private')) {
            $response->setPublic();
            foreach ($this->private_headers as $private_header) {
                if ($request->headers->has($private_header)) {
                    $response->setPrivate();
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param Response $response
     * @param \DateTime $last_modified
     *
     * @return ResponseConfigurator
     */
    protected function setLastModified(Response $response, \DateTime $last_modified)
    {
        $response
            ->setLastModified($last_modified)
            ->setDate($last_modified)
            ->headers
            ->addCacheControlDirective('must-revalidate', true);

        return $this;
    }

    /**
     * Set max-age, s-maxage and expires headers
     *
     * Set $lifetime as < 0 for not set max-age
     * Need set Cache-Control public/private before set lifetime
     *
     * @param Response $response
     * @param int $lifetime
     *
     * @return ResponseConfigurator
     */
    protected function setLifetime(Response $response, $lifetime)
    {
        if ($lifetime >= 0) {
            $date = clone $response->getDate();
            $response
                ->setMaxAge($lifetime)
                ->setExpires($date->modify(sprintf('now +%s seconds', $lifetime)));

            if (!$response->headers->hasCacheControlDirective('private')) {
                $response->setSharedMaxAge($lifetime);
            }
        }

        return $this;
    }

    /**
     * Set ETag
     *
     * Need set Last-Modified before ETag
     *
     * @param Response $response
     * @param Request $request
     *
     * @return ResponseConfigurator
     */
    protected function setEtag(Response $response, Request $request)
    {
        if (!$response->getEtag()) {
            $response->setEtag($this->key_builder->getEtag($request, $response));
        }

        return $this;
    }
}
