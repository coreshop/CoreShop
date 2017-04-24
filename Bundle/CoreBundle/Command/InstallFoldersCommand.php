<?php

namespace CoreShop\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallFoldersCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:install:folders')
            ->setDescription('Install CoreShop Object Folders.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates CoreShop Object Folders.
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
            'Creating CoreShop Folders <info>%s</info>.',
            $this->getEnvironment()
        ));

        $this
            ->get('coreshop.installer.executor.folder_installer')
            ->installFolders()
        ;

        return 0;
    }
}
