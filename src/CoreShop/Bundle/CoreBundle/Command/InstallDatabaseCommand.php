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
use CoreShop\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProviderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

final class InstallDatabaseCommand extends AbstractInstallCommand
{
    public function __construct(
        KernelInterface $kernel,
        CommandDirectoryChecker $directoryChecker,
        protected DatabaseSetupCommandsProviderInterface $databaseSetupCommand,
    ) {
        parent::__construct($kernel, $directoryChecker);
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:install:database')
            ->setDescription('Install CoreShop database.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command creates CoreShop database.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Creating CoreShop database for environment <info>%s</info>.',
            $this->getEnvironment(),
        ));

        $commands = $this->databaseSetupCommand->getCommands($input, $output, $this->getHelper('question'));

        $this->runCommands($commands, $output);
        $outputStyle->newLine();

        return 0;
    }
}
