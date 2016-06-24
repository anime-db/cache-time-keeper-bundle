<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder\EtagHasherInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;

class CacheKeyBuilder
{
    /**
     * @var string
     */
    const IDENTIFIER_SEPARATOR = '|';

    /**
     * @var string
     */
    const IDENTIFIER_PREFIX = ':';

    /**
     * @var EtagHasherInterface
     */
    protected $etag_hasher;

    /**
     * @var Registry|null
     */
    protected $doctrine;

    /**
     * @param EtagHasherInterface $etag_hasher
     */
    public function __construct(EtagHasherInterface $etag_hasher)
    {
        $this->etag_hasher = $etag_hasher;
    }

    /**
     * @param Registry $doctrine
     *
     * @return CacheKeyBuilder
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    /**
     * @param object $entity
     *
     * @return string|null
     */
    public function getEntityAlias($entity)
    {
        if (!($this->doctrine instanceof Registry)) {
            return null;
        }

        $class = get_class($entity);

        $namespaces = $this
            ->doctrine
            ->getManager()
            ->getConfiguration()
            ->getEntityNamespaces();

        foreach ($namespaces as $ns_alias => $ns) {
            if (strpos($class, $ns) === 0) {
                return $ns_alias.':'.ltrim(str_replace($ns, '', $class), '\\');
            }
        }

        return null;
    }

    /**
     * @param object $entity
     *
     * @return string|null
     */
    public function getEntityIdentifier($entity)
    {
        if (!($this->doctrine instanceof Registry)) {
            return null;
        }

        $ids = $this
            ->doctrine
            ->getManager()
            ->getClassMetadata(get_class($entity))
            ->getIdentifierValues($entity);

        return $ids ? self::IDENTIFIER_PREFIX.implode(self::IDENTIFIER_SEPARATOR, $ids) : null;
    }

    /**
     * @param Response $response
     *
     * @return string
     */
    public function getEtag(Response $response)
    {
        return $this->etag_hasher->hash($response);
    }
}
