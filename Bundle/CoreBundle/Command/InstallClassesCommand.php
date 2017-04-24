<?php

namespace CoreShop\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallClassesCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:install:classes')
            ->setDescription('Install CoreShop Classes.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates CoreShop Classes.
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
            'Creating CoreShop Pimcore classes <info>%s</info>.',
            $this->getEnvironment()
        ));

        $this
            ->get('coreshop.installer.executor.class_installer')
            ->installClasses($output)
        ;

        return 0;
    }
}
