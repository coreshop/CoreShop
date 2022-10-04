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

namespace CoreShop\Bundle\ResourceBundle\Command;

use CoreShop\Bundle\ResourceBundle\Installer\ResourceInstallerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallResourcesCommand extends Command
{
    public function __construct(
        protected ResourceInstallerInterface $resourceInstaller,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:resources:install')
            ->setDescription('Install Resources.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command install Resources. (Like Static Routes or Pimcore Classes)
EOT
            )
            ->addOption(
                'application-name',
                'a',
                InputOption::VALUE_REQUIRED,
                'Application Name',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var Application $application
         */
        $application = $this->getApplication();
        $kernel = $application->getKernel();

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Install Resources for Environment <info>%s</info>.',
            $kernel->getEnvironment(),
        ));

        $this->resourceInstaller->installResources($output, $input->getOption('application-name'));

        return 0;
    }
}
