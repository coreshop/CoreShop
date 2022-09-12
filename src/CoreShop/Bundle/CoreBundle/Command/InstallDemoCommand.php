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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InstallDemoCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setName('coreshop:install:demo')
            ->setDescription('Install CoreShop Demo Data.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command install CoreShop Demo Data.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->runCommands(['coreshop:fixture:data:load' => ['--fixtures-type' => 'demo']], $output);

        return 0;
    }
}
