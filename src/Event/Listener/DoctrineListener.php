<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineListener
{
    /**
     * @var Keeper
     */
    protected $keeper;

    /**
     * @var bool
     */
    protected $track_individually_entity;

    /**
     * @param Keeper $keeper
     * @param bool $track_individually_entity
     */
    public function __construct(Keeper $keeper, $track_individually_entity)
    {
        $this->keeper = $keeper;
        $this->track_individually_entity = $track_individually_entity;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->update($args, false);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->update($args, true);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->update($args, false);
    }

    /**
     * @param LifecycleEventArgs $args
     * @param bool $remove
     */
    protected function update(LifecycleEventArgs $args, $remove)
    {
        $alias = $this->getEntityAlias($args);
        $this->keeper->set($alias, new \DateTime());

        if ($this->track_individually_entity && ($ids = $this->getEntityIdentifier($args))) {
            if ($remove) {
                $this->keeper->remove($alias.$ids);
            } else {
                $this->keeper->set($alias.$ids, new \DateTime());
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return string
     */
    protected function getEntityAlias(LifecycleEventArgs $args)
    {
        $class = get_class($args->getEntity());

        foreach ($args->getEntityManager()->getConfiguration()->getEntityNamespaces() as $ns_alias => $ns) {
            if (strpos($class, $ns) === 0) {
                return $ns_alias.':'.ltrim(str_replace($ns, '', $class), '\\');
            }
        }

        throw new \RuntimeException(sprintf('Entity "%s" is not supported from EntityManager', $class));
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return string
     */
    protected function getEntityIdentifier(LifecycleEventArgs $args)
    {
        $ids = $args
            ->getEntityManager()
            ->getClassMetadata(get_class($args->getEntity()))
            ->getIdentifierValues($args->getEntity());

        return $ids ? Keeper::IDENTIFIER_PREFIX.implode(Keeper::IDENTIFIER_SEPARATOR, $ids) : '';
    }
}
