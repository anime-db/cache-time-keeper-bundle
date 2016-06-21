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
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface|null
     */
    protected $em;

    /**
     * @param EtagHasherInterface $etag_hasher
     */
    public function __construct(EtagHasherInterface $etag_hasher)
    {
        $this->etag_hasher = $etag_hasher;
    }

    /**
     * @param EntityManagerInterface $em
     *
     * @return CacheKeyBuilder
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * @param object $entity
     *
     * @return string|null
     */
    public function getEntityAlias($entity)
    {
        if (!($this->em instanceof EntityManagerInterface)) {
            return null;
        }

        $class = get_class($entity);

        foreach ($this->em->getConfiguration()->getEntityNamespaces() as $ns_alias => $ns) {
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
        if (!($this->em instanceof EntityManagerInterface)) {
            return null;
        }

        $ids = $this->em->getClassMetadata(get_class($entity))->getIdentifierValues($entity);

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
