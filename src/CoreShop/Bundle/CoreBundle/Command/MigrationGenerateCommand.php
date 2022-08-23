<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MigrationGenerateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('coreshop:migration:generate')
            ->setHidden(true)
            ->setDescription('Create a new  CoreShop migrations.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);

        $commandExecutor = new CommandExecutor($input, $output, $application);
        $commandExecutor->runCommand('doctrine:migrations:generate', ['--namespace' => 'CoreShop\\Bundle\\CoreBundle\\Migrations'], $output);

        $output->writeln('');

        return 0;
    }
}
