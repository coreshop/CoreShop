<?php

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
            'command' => 'database',
            'message' => 'Setting up the database.',
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

        $this->ensureDirectoryExistsAndIsWritable($this->getContainer()->getParameter('kernel.cache_dir'), $output);

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
