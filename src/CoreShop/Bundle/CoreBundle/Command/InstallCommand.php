<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Bundle\CoreBundle\Installer;
use CoreShop\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends AbstractInstallCommand
{
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

    protected function configure(): void
    {
        $this
            ->setName('coreshop:install')
            ->setDescription('Installs CoreShop.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs CoreShop.
EOT
            );
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

        $outputStyle->newLine(2);
        $outputStyle->success($this->getProperFinalMessage($errored));
        $outputStyle->writeln(sprintf(
            'You can now open your store at the following path under the website root: <info>/</info>'
        ));

        return 0;
    }

    private function getProperFinalMessage(true $errored): string
    {
        if ($errored) {
            return 'CoreShop has been installed, but some error occurred.';
        }

        return 'CoreShop has been successfully installed.';
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
}
