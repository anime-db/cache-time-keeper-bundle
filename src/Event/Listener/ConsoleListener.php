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
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class ConsoleListener
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
     * @param ConsoleTerminateEvent $event
     */
    public function onTerminate(ConsoleTerminateEvent $event)
    {
        if ($event->getCommand()->getName() == 'cache:clear') {
            $this->keeper->set(Keeper::LAST_UPDATE_KEY, new \DateTime());
        }
    }
}
