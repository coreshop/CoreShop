<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Command;

use CoreShop\Bundle\ResourceBundle\Installer\ResourceInstallerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallResourcesCommand extends ContainerAwareCommand
{
    /**
     * @var ResourceInstallerInterface
     */
    protected $resourceInstaller;

    /**
     * @param ResourceInstallerInterface $resourceInstaller
     */
    public function __construct(ResourceInstallerInterface $resourceInstaller)
    {
        $this->resourceInstaller = $resourceInstaller;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:resources:install')
            ->setDescription('Install Resources.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command install Resources. (Like Static Routes or Pimcore Classes)
EOT
            )
            ->addOption(
                'application-name',
                'a',
                InputOption::VALUE_REQUIRED,
                'Application Name'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var Application $application
         */
        $application = $this->getApplication();
        $kernel = $application->getKernel();

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Install Resources for Environment <info>%s</info>.',
            $kernel->getEnvironment()
        ));

        $this->resourceInstaller->installResources($output, $input->getOption('application-name'));

        return 0;
    }
}
