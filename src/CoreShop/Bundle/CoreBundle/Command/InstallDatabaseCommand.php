<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
        protected DatabaseSetupCommandsProviderInterface $databaseSetupCommand
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Creating CoreShop database for environment <info>%s</info>.',
            $this->getEnvironment()
        ));

        $commands = $this->databaseSetupCommand->getCommands($input, $output, $this->getHelper('question'));

        $this->runCommands($commands, $output);
        $outputStyle->newLine();

        return 0;
    }
}
