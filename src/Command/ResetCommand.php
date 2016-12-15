<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Command;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCommand extends Command
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
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cache:reset-cache-time-keeper')
            ->setDescription('Reset last update date of the project')
            ->setHelp('Command only reset date of update. Not remove data from the storage.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->keeper->set(Keeper::LAST_UPDATE_KEY, new \DateTime());
        $output->writeln('Reset last update date of the project is complete.');
    }
}
