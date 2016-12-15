<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Command;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Command\ResetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCommandTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Keeper
     */
    protected $keeper;

    /**
     * @var ResetCommand
     */
    protected $command;

    protected function setUp()
    {
        $this->keeper = $this->getNoConstructorMock(Keeper::class);
        $this->command = new ResetCommand($this->keeper);
    }

    public function testConfigure()
    {
        $this->assertEquals('cache:reset-cache-time-keeper', $this->command->getName());
        $this->assertEquals('Reset last update date of the project', $this->command->getDescription());
        $this->assertEquals(
            'Command only reset date of update. Not remove data from the storage.',
            $this->command->getHelp()
        );
    }

    public function testExecute()
    {
        /** @var $input \PHPUnit_Framework_MockObject_MockObject|InputInterface */
        $input = $this->getMock(InputInterface::class);

        /** @var $output \PHPUnit_Framework_MockObject_MockObject|OutputInterface */
        $output = $this->getMock(OutputInterface::class);
        $output
            ->expects($this->once())
            ->method('writeln')
            ->with('Reset last update date of the project is complete.');

        $this->keeper
            ->expects($this->once())
            ->method('set')
            ->will($this->returnCallback(function ($key, $time) {
                $this->assertEquals(Keeper::LAST_UPDATE_KEY, $key);
                $this->assertInstanceOf(\DateTime::class, $time);
                $this->assertTrue($time >= new \DateTime('-1 second'));

                return true;
            }));

        $this->command->run($input, $output);
    }
}
