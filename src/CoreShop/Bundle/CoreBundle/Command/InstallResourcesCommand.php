<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InstallResourcesCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setName('coreshop:install:resources')
            ->setDescription('Install CoreShop Resources.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates CoreShop Resources.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->runCommands(['coreshop:resources:install' => ['--application-name' => 'coreshop']], $output, false, true);

        return 0;
    }
}
