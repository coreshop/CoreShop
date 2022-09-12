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
