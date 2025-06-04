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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PimcoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AppMigrateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('coreshop:app:migrate')
            ->setDescription('Execute App migrations.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> executes all CoreShop migrations.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);

        $parameters = array_merge(
            ['command' => 'doctrine:migrations:migrate'],
            ['--prefix' => 'App\\Migrations'],
        );

        $this->getApplication()->setAutoExit(false);
        $exitCode = $this->getApplication()->run(new ArrayInput($parameters), $output);

        if (0 !== $exitCode) {
            $this->getApplication()->setAutoExit(true);

            $errorMessage = sprintf('The command terminated with an error code: %u.', $exitCode);
            $output->writeln("<error>$errorMessage</error>");

            throw new \Exception($errorMessage, $exitCode);
        }

        return 0;
    }
}
