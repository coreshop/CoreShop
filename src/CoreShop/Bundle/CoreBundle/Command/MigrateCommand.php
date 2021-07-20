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
use Pimcore\Tool\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class MigrateCommand extends Command
{
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);

        $commandExecutor = new CommandExecutor($input, $output, $application);
        $commandExecutor->runCommand('doctrine:migrations:migrate', ['--prefix' => 'CoreShop\\Bundle\\CoreBundle\\Migrations'], $output);

        $output->writeln('');

        return 0;
    }
}
