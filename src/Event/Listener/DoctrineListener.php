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
        $this->keeper->set($this->getEntityAlias($args), new \DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getEntityAlias($args), new \DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->keeper->set($this->getEntityAlias($args), new \DateTime());
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
}
