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
    private $keeper;

    /**
     * @var bool
     */
    private $track_clear_cache;

    /**
     * @var array
     */
    private $commands = [
        'cache:clear',
        'cache:warmup',
    ];

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
        if ($this->track_clear_cache && in_array($event->getCommand()->getName(), $this->commands)) {
            $this->keeper->set(Keeper::LAST_UPDATE_KEY, new \DateTime());
        }
    }
}
