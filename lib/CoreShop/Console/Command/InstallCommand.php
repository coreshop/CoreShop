<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Console\Command;



use CoreShop\Plugin\Install;
use Pimcore\Console\AbstractCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 * @package CoreShop\Console\Command
 */
class InstallCommand extends AbstractCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:install')
            ->setDescription('Install CoreShop')
            ->addOption(
                'sql-only', 'sql',
                InputOption::VALUE_NONE,
                'Install SQL Database Only'
            );
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableLogging();
        $sqlOnly = $input->getOption("sql-only");

        $install = new Install();

        if ($sqlOnly) {
            $install->executeSQL('CoreShop');
            $install->executeSQL('CoreShop-States');
        }
        else {
            if ($install->fullInstall()) {
                $this->output->writeln('');
                $this->output->writeln('<info>Done</info>');
            } else {
                $this->writeError("<error>Error installing CoreShop</error>");
                return 100;
            }
        }

        return 0;
    }
}
