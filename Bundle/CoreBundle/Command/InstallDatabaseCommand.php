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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallDatabaseCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:install:database')
            ->setDescription('Install CoreShop database.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates CoreShop database.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Creating CoreShop database for environment <info>%s</info>.',
            $this->getEnvironment()
        ));

        $commands = $this
            ->get('coreshop.commands_provider.database_setup')
            ->getCommands($input, $output, $this->getHelper('question'))
        ;

        $this->runCommands($commands, $output);
        $outputStyle->newLine();

        $this->commandExecutor->runCommand('coreshop:install:fixtures', [], $output);
    }
}
