<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;

use Symfony\Component\HttpFoundation\Response;

interface EtagHasherInterface
{
    /**
     * @param Response $response
     *
     * @return string
     */
    public function hash(Response $response);
}
