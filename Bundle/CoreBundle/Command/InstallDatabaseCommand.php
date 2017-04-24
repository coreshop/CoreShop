<?php

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
