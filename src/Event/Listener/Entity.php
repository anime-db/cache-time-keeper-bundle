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
     * @var Keeper
     */
    protected $keeper;

    /**
     * @param Keeper $keeper
     */
    public function __construct(Keeper $keeper)
    {
        $this->keeper = $keeper;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getKeyFromEntity($args), new \DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getKeyFromEntity($args), new \DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getKeyFromEntity($args), new \DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return string
     */
    protected function getKeyFromEntity(LifecycleEventArgs $args)
    {
        $parts = explode('\\', get_class($args->getEntity()));
        $entity = array_pop($parts);
        $namespace = implode('\\', $parts);

        $namespaces = $args->getEntityManager()->getConfiguration()->getEntityNamespaces();
        foreach ($namespaces as $ns_alias => $ns) {
            if ($ns == $namespace) {
                return $ns_alias.':'.$entity;
            }
        }
    }
}
