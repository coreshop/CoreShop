<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use CoreShop\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractInstallCommand extends Command
{
    protected CommandExecutor $commandExecutor;

    public function __construct(
        protected KernelInterface $kernel,
        protected CommandDirectoryChecker $directoryChecker,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $application = $this->getApplication();

        if (null === $application) {
            throw new \InvalidArgumentException('application is null');
        }

        $application->setCatchExceptions(false);

        $this->commandExecutor = new CommandExecutor($input, $output, $application);
    }

    protected function getEnvironment(): string
    {
        return $this->kernel->getEnvironment();
    }

    protected function isDebug(): bool
    {
        return $this->kernel->isDebug();
    }

    protected function renderTable(array $headers, array $rows, OutputInterface $output): void
    {
        $table = new Table($output);

        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->render()
        ;
    }

    protected function createProgressBar(OutputInterface $output, int $length = 10): ProgressBar
    {
        $progress = new ProgressBar($output);
        $progress->setBarCharacter('<info>░</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('<comment>░</comment>');

        $progress->start($length);

        return $progress;
    }

    protected function runCommands(array $commands, OutputInterface $output, bool $displayProgress = true, bool $passOutput = false): void
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
            //$this->get('doctrine')->getManager()->getConnection()->close();

            if ($displayProgress && $progress) {
                $progress->advance();
            }
        }

        if ($displayProgress && $progress) {
            $progress->finish();
        }
    }

    protected function ensureDirectoryExistsAndIsWritable($directory, OutputInterface $output): void
    {
        $this->directoryChecker->setCommandName($this->getName());

        $this->directoryChecker->ensureDirectoryExists($directory, $output);
        $this->directoryChecker->ensureDirectoryIsWritable($directory, $output);
    }
}
