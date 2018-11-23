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

use CoreShop\Bundle\CoreBundle\Installer;
use CoreShop\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Pimcore\Migrations\MigrationManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends AbstractInstallCommand
{
    /**
     * @var array
     */
    private $commands = [
        [
            'command' => 'resources',
            'message' => 'Install Pimcore Classes.',
        ],
        [
            'command' => 'database',
            'message' => 'Setting up the database.',
        ],
        [
            'command' => 'folders',
            'message' => 'Install CoreShop Object Folders.',
        ],
    ];

    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var MigrationManager
     */
    private $migrationManager;

    /**
     * @var Bundle
     */
    private $bundle;

    /**
     * @param KernelInterface         $kernel
     * @param CommandDirectoryChecker $directoryChecker
     * @param Installer               $installer
     * @param MigrationManager        $migrationManager
     * @param Bundle                  $bundle
     */
    public function __construct(
        KernelInterface $kernel,
        CommandDirectoryChecker $directoryChecker,
        Installer $installer,
        MigrationManager $migrationManager,
        Bundle $bundle
    ) {
        parent::__construct($kernel, $directoryChecker);

        $this->installer = $installer;
        $this->migrationManager = $migrationManager;
        $this->bundle = $bundle;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:install')
            ->setDescription('Installs CoreShop.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs CoreShop.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Installing CoreShop...</info>');
        $outputStyle->writeln($this->getCoreShopLogo());

        $this->ensureDirectoryExistsAndIsWritable($this->kernel->getCacheDir(), $output);

        $errored = false;
        foreach ($this->commands as $step => $command) {
            try {
                $outputStyle->newLine();
                $outputStyle->section(sprintf(
                    'Step %d of %d. <info>%s</info>',
                    $step + 1,
                    count($this->commands),
                    $command['message']
                ));
                $this->commandExecutor->runCommand('coreshop:install:' . $command['command'], [], $output);
            } catch (RuntimeException $exception) {
                $errored = true;
            }
        }

        $installConfiguration = $this->installer->getInstallMigrationConfiguration();
        $this->migrationManager->markVersionAsMigrated($installConfiguration->getVersion($installConfiguration->getLatestVersion()));

        $migrationConfiguration = $this->migrationManager->getBundleConfiguration($this->bundle);
        $this->migrationManager->markVersionAsMigrated($migrationConfiguration->getVersion($migrationConfiguration->getLatestVersion()));

        $outputStyle->newLine(2);
        $outputStyle->success($this->getProperFinalMessage($errored));
        $outputStyle->writeln(sprintf(
            'You can now open your store at the following path under the website root: <info>/</info>'
        ));
    }

    /**
     * @param bool $errored
     *
     * @return string
     */
    private function getProperFinalMessage($errored)
    {
        if ($errored) {
            return 'CoreShop has been installed, but some error occurred.';
        }

        return 'CoreShop has been successfully installed.';
    }

    /**
     * @return string
     */
    private function getCoreShopLogo()
    {
        return '   
                                    <info>;##:</info>
                                    <info>#`;#</info>
                                    <info>#  #</info>
                                   <info>.#  #</info>
                                   <info>:#  #</info>
                                   <info>;#::,</info>
                                <info>::::@:::.</info>
                            <info>`:::::: # :::</info>
                         <info>`::::::::: #.@::`</info>
                        <info>::::::::::::,#`:::</info>
                       <info>:::::::::::::. ::::</info>
                      <info>`::::::::::::::::::::</info>
                      <info>:::::::::::::::::::::</info>
                      <info>:::::::::::::::::::::,</info>
                     <info>:::::::::::::::::::::::</info>
                     <info>:::::::::::::::::::::::`</info>
                    <info>:::::::::::::::::::::::::</info>
                    <info>:::::::::::::::::::::::::</info>
                   <info>::::::::::::::::::::::::::,</info>
                   <info>:::::::::::::::::::::::::::</info>
                  <info>,::::::::::::::::::::::::::.</info>
                  <info>:::::::::::::::::::::::::::</info>
                 <info>.:::::::::::::::::::::::::::</info>
                 <info>:::::::::::::::::::::::::::</info>
                <info>`:::::::::::::::::::::::::::</info>
                <info>:::::::::::::::::::::::::::</info>
                <info>:::::::::::::::::::::::::::</info>
               <info>:::::::::::::::::::::::::::</info>
               <info>:::::::::::::::::::::::::::</info>
              <info>:::::::::::::::::::::::::::`</info>
              <info>:::::::::::::::::::::::::::</info>
             <info>,::::::::::::::::::::::::::.</info>
             <info>:::::::::::::::::::::::::::</info>
            <info>.::::::::::::::::::::::::::,</info>
            <info>:::::::::::::::::::::::::::</info>
            <info>`::::::::::::::::::::::::::</info>
             <info>.::::::::::::::::::::::::</info>
               <info>`::::::::::::::::::::::</info>
                  <info>:::::::::::::::::::</info>
                    <info>:::::::::::::::::</info>
                      <info>::::::::::::::</info>
                        <info>.:::::::::::</info>
                          <info>`::::::::`</info>
                             <info>::::::</info>
                               <info>:::</info>
';
    }
}
