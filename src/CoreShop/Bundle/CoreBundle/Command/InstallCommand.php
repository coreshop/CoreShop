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
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends AbstractInstallCommand
{
    /**
     * @var array
     */
    private $commands = [
        [
            'command' => 'classes',
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
        [
            'command' => 'assets',
            'message' => 'Install CoreShop Assets.',
        ]
    ];

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
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
                $this->commandExecutor->runCommand('coreshop:install:'.$command['command'], [], $output);
            } catch (RuntimeException $exception) {
                $errored = true;
            }
        }

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
'
        ;
    }
}
