<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineListener
{
    /**
     * @var Keeper
     */
    protected $keeper;

    /**
     * @var CacheKeyBuilder
     */
    protected $builder;

    /**
     * @var bool
     */
    protected $track_individually_entity;

    /**
     * @param Keeper $keeper
     * @param CacheKeyBuilder $builder
     * @param bool $track_individually_entity
     */
    public function __construct(Keeper $keeper, CacheKeyBuilder $builder, $track_individually_entity)
    {
        $this->keeper = $keeper;
        $this->builder = $builder;
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
        $alias = $this->builder->getEntityAlias($args->getEntity(), $args->getEntityManager());
        $this->keeper->set($alias, new \DateTime());

        if ($this->track_individually_entity) {
            $ids = $this->builder->getEntityIdentifier($args->getEntity(), $args->getEntityManager());
            if ($ids !== null) {
                if ($remove) {
                    $this->keeper->remove($alias.$ids);
                } else {
                    $this->keeper->set($alias.$ids, new \DateTime());
                }
            }
        }
    }
}
