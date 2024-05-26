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

use CoreShop\Bundle\CoreBundle\Installer;
use CoreShop\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends AbstractInstallCommand
{
    /**
     * @var array<int, array>
     */
    private array $commands = [
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

    public function __construct(
        KernelInterface $kernel,
        CommandDirectoryChecker $directoryChecker,
        protected Installer $installer,
    ) {
        parent::__construct($kernel, $directoryChecker);
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:install')
            ->setDescription('Installs CoreShop.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command installs CoreShop.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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
                $outputStyle->section(
                    sprintf(
                        'Step %d of %d. <info>%s</info>',
                        $step + 1,
                        count($this->commands),
                        $command['message'],
                    ),
                );
                $this->commandExecutor->runCommand('coreshop:install:' . $command['command'], [], $output);
            } catch (RuntimeException) {
                $errored = true;
            }
        }

        $this->installer->markAllMigrationsInstalled();

        $outputStyle->newLine(2);
        $outputStyle->success($this->getProperFinalMessage($errored));
        $outputStyle->writeln(
            'You can now open your store at the following path under the website root: <info>/</info>',
        );

        return 0;
    }

    private function getCoreShopLogo(): string
    {
        return '<fg=red>                                          
                                       %%%%%%%%%%                                    
                                   %%%%%%%%%%%%%%%%%%%                               
                               %%%%%%%%%%%%%%%%%%%%%%%%%%%                           
                           %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%                       
                      %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%                   
                  %%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%              
             %%%%%%%%%%%%%%%%%%%%%%         %        %%%%%%%%%%%%%%%%%%%%%%          
         %%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%      
     %%%%%%%%%%%%%%%%%%%%%          %%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%% 
    %%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%%%%%%        %%%%%%%%%%%%%%%%%% 
    %%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%% 
    %%%%%%%%%%%        %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%% 
    %%%%%%%%%%%      %%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%        %%%%% 
    %%%%%%%%%%%      %%%%%%%%%%%%%%%                 %%%%%%%%%%%%%%%%%%%%%%        % 
    %%%%%%%%%%%      %%%%%%%%%%%                         %%%%%%%%%%%%%%%%%%%%%%      
    %%%%%%%%%%%      %%%%%%%%%%                               %%%%%%%%%%%%%%%%%%%%%  
    %%%%%%%%%%%      %%%%%%%%%%                                   %%%%%%%%%%%%%%%%%% 
    %%%%%%%%%%%        %%%%%%%%                          %%%%         %%%%%%%%%%%%%% 
    %%%%%%%%%%%%%%%         %%%                          %%%%%%%%%        %%%%%%%%%% 
    %%%%%%%%%%%%%%%%%%%                                  %%%%%%%%%%%      %%%%%%%%%% 
      %%%%%%%%%%%%%%%%%%%%%%                             %%%%%%%%%%%      %%%%%%%%%% 
          %%%%%%%%%%%%%%%%%%%%%%                         %%%%%%%%%%%      %%%%%%%%%% 
    %%        %%%%%%%%%%%%%%%%%%%%%%                %%%%%%%%%%%%%%%%      %%%%%%%%%% 
    %%%%%%         %%%%%%%%%%%%%%%%%%%%%        %%%%%%%%%%%%%%%%%%%%      %%%%%%%%%% 
    %%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%        %%%%%%%%%% 
    %%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%% 
    %%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%% 
     %%%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%          %%%%%%%%%%%%%%%%%%%%%  
          %%%%%%%%%%%%%%%%%%%%%%        %%%%%%%%         %%%%%%%%%%%%%%%%%%%%%%      
              %%%%%%%%%%%%%%%%%%%%%%                 %%%%%%%%%%%%%%%%%%%%%%          
                   %%%%%%%%%%%%%%%%%%%%%         %%%%%%%%%%%%%%%%%%%%%               
                       %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%                   
                           %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%                       
                               %%%%%%%%%%%%%%%%%%%%%%%%%%                            
                                    %%%%%%%%%%%%%%%%%                                
                                        %%%%%%%%%                                                                                                                                 
</>';
    }

    private function getProperFinalMessage(bool $errored): string
    {
        if ($errored) {
            return 'CoreShop has been installed, but some error occurred.';
        }

        return 'CoreShop has been successfully installed.';
    }
}
