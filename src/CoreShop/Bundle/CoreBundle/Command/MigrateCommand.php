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

use CoreShop\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Pimcore\Migrations\Version;
use Pimcore\Tool\Console;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class MigrateCommand extends Command
{
    /**
     * @var array
     */
    protected $dependantBundles = [];

    public function __construct(array $dependantBundles)
    {
        parent::__construct();

        $this->dependantBundles = $dependantBundles;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('coreshop:migrate')
            ->setDescription('Execute CoreShop migrations.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> executes all CoreShop migrations.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);

        $commandExecutor = new CommandExecutor($input, $output, $application);
        $commandExecutor->runCommand('pimcore:migrations:migrate', ['--bundle' => 'CoreShopCoreBundle'], $output);

        $phpCli = Console::getPhpCli();

        $output->writeln('');

        foreach ($this->dependantBundles as $bundle) {
            $process = new Process(
                 array_merge(
                    [$phpCli],
                    [
                        'bin/console',
                        'pimcore:migrations:migrate',
                        '--bundle='.$bundle,
                    ]
                )
            );
            $process->setTty(true);
            $process->run();
        }

        return 0;
    }
}
