<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractInstallCommand extends Command
{
    /**
     * @var CommandExecutor
     */
    protected $commandExecutor;

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);

        if (null === $application) {
            throw new \InvalidArgumentException('application is null');
        }

        $this->commandExecutor = new CommandExecutor($input, $output, $application);
    }

    /**
     * @param string $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @return string
     */
    protected function getEnvironment()
    {
        return $this->get('kernel')->getEnvironment();
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return $this->get('kernel')->isDebug();
    }

    /**
     * @param array           $headers
     * @param array           $rows
     * @param OutputInterface $output
     */
    protected function renderTable(array $headers, array $rows, OutputInterface $output)
    {
        $table = new Table($output);

        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->render()
        ;
    }

    /**
     * @param OutputInterface $output
     * @param int             $length
     *
     * @return ProgressBar
     */
    protected function createProgressBar(OutputInterface $output, $length = 10)
    {
        $progress = new ProgressBar($output);
        $progress->setBarCharacter('<info>░</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('<comment>░</comment>');

        $progress->start($length);

        return $progress;
    }

    /**
     * @param array           $commands
     * @param OutputInterface $output
     * @param bool            $displayProgress
     * @param bool            $passOutput
     */
    protected function runCommands(array $commands, OutputInterface $output, $displayProgress = true, $passOutput = false)
    {
        $progress = null;

        if ($displayProgress) {
            $progress = $this->createProgressBar($output, count($commands));
        }

        foreach ($commands as $key => $value) {
            if (is_string($key)) {
                $command = $key;
                $parameters = $value;
            } else {
                $command = $value;
                $parameters = [];
            }

            $this->commandExecutor->runCommand($command, $parameters, $passOutput ? $output : null);

            // PDO does not always close the connection after Doctrine commands.
            // See https://github.com/symfony/symfony/issues/11750.
            $this->get('doctrine')->getManager()->getConnection()->close();

            if ($displayProgress && $progress) {
                $progress->advance();
            }
        }

        if ($displayProgress && $progress) {
            $progress->finish();
        }
    }

    /**
     * @param string          $directory
     * @param OutputInterface $output
     */
    protected function ensureDirectoryExistsAndIsWritable($directory, OutputInterface $output)
    {
        $checker = $this->get('coreshop.installer.checker.command_directory');
        $checker->setCommandName($this->getName());

        $checker->ensureDirectoryExists($directory, $output);
        $checker->ensureDirectoryIsWritable($directory, $output);
    }
}
