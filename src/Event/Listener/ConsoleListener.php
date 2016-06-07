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
     * @var bool
     */
    protected $track_clear_cache;

    /**
     * @param Keeper $keeper
     * @param bool $track_clear_cache
     */
    public function __construct(Keeper $keeper, $track_clear_cache)
    {
        $this->keeper = $keeper;
        $this->track_clear_cache = $track_clear_cache;
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onTerminate(ConsoleTerminateEvent $event)
    {
        if ($this->track_clear_cache && $event->getCommand()->getName() == 'cache:clear') {
            $this->keeper->set(Keeper::LAST_UPDATE_KEY, new \DateTime());
        }
    }
}
