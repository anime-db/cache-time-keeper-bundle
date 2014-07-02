<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Entity listener
 *
 * @package AnimeDb\Bundle\CacheTimeKeeperBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Entity
{
    /**
     * Keeper
     *
     * @var \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper
     */
    protected $keeper;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper $keeper
     */
    public function __construct(Keeper $keeper)
    {
        $this->keeper = $keeper;
    }

    /**
     * Post persist
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getKeyFromEntity($args), new \DateTime());
    }

    /**
     * Post remove
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getKeyFromEntity($args), new \DateTime());
    }

    /**
     * Post update
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getKeyFromEntity($args), new \DateTime());
    }

    /**
     * Get key from entity
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     *
     * @return string
     */
    protected function getKeyFromEntity(LifecycleEventArgs $args)
    {
        return $args->getEntityManager()->getClassMetadata(get_class($args->getEntity()))->getName();
    }
}