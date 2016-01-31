<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Console\Command;

use Pimcore\Console\AbstractCommand;
use Pimcore\Tool\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Pimcore\Tool\Admin;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use CoreShop\Plugin\Update;

class UpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('coreshop:internal:update')
            ->setDescription('Update CoreShop to the desired build')
            ->addOption(
                'list', 'l',
                InputOption::VALUE_NONE,
                "List available updates"
            )
            ->addOption(
                'update', 'u',
                InputOption::VALUE_OPTIONAL,
                'Update to recent build'
            )
            ->addOption(
                'dry-run', 'd',
                InputOption::VALUE_NONE,
                'Dry-run'
            )
            ->addArgument("config");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption("dry-run");
        $currentRevision = null;

        if ($dryRun) {
            $this->output->writeln("<info>---------- DRY-RUN ----------</info>");
        }

        $updater = new Update();

        $updater->setDryRun($dryRun);

        $availableUpdates = $updater->getAvailableBuildList();

        if ($input->getOption("list")) {
            if ($availableUpdates !== false && !empty($availableUpdates)) {
                $rows = [];

                foreach ($availableUpdates as $release) {
                    $rows[] = array( $release["build"] );
                }

                $table = new Table($output);
                $table
                    ->setHeaders(array("Build"))
                    ->setRows($rows);
                $table->render();

                $latest = end($availableUpdates);

                $this->output->writeln("The latest available build is: <comment>" . $latest["build"] . "</comment>");
            } else {
                $this->output->writeln("<info>No updates available</info>");
            }
        }

        if ($input->getOption("update")) {
            if ($availableUpdates == false || empty($availableUpdates)) {
                $this->writeError("No update found.");
                exit;
            }

            $latest = end($availableUpdates);

            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion("You are going to update to build <comment>" . $latest['build'] . "</comment> Continue with this action? (y/n)", false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }

            $this->output->writeln("Starting the update process ...");

            $maintenanceModeId = 'cache-warming-dummy-session-id';
            Admin::activateMaintenanceMode($maintenanceModeId);

            $stoppedByError = false;
            $lastError = null;

            $execution = $updater->updateCoreData();

            Admin::deactivateMaintenanceMode();

            $this->output->writeln("\n");

            if ($stoppedByError) {
                $this->output->writeln("<error>Update stopped by error! Please check your logs</error>");
                $this->output->writeln("Last return value was: " . $lastError);
            } else {
                $this->output->writeln("<info>Update done!</info>");

                if ($execution['success'] === true && !empty($execution['log'])) {
                    $table = new Table($output);
                    $table
                        ->setHeaders(array('Build', 'Message'))
                        ->setRows($execution['log']);
                    $table->render();
                }
            }
        }
    }
}
