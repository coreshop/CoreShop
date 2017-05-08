<?php

namespace CoreShop\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InstallFixturesCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:install:fixtures')
            ->setDescription('Install CoreShop Main Fixtures.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command install CoreShop Main Fixtures.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runCommands(['okvpn:migration:data:load'], $output);
    }
}
