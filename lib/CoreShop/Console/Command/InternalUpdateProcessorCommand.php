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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Console\Command;

use Pimcore\Console\AbstractCommand;
use Pimcore\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CoreShop\Update;

/**
 * Class InternalUpdateProcessorCommand
 * @package CoreShop\Console\Command
 */
class InternalUpdateProcessorCommand extends AbstractCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:internal:update-processor')
            ->setDescription('For internal use only')
            ->addArgument('config');
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
        $status = ['success' => true];
        $config = $input->getArgument('config');

        if ($config) {
            $job = json_decode($config, true);

            if (is_array($job)) {
                if (isset($job['dry-run'])) {
                    // do not do anything here
                    Logger::info('skipped update job because it is in dry-run mode', $job);
                } elseif ($job['type'] == 'deleteFile') {
                    Update::deleteData($job['url']);
                } elseif ($job['type'] == 'files') {
                    Update::installData($job['revision']);
                } elseif ($job['type'] == 'clearcache') {
                    \Pimcore\Cache::clearAll();
                } elseif ($job['type'] == 'preupdate') {
                    $status = Update::executeScript($job['revision'], 'preupdate');
                } elseif ($job['type'] == 'postupdate') {
                    $status = Update::executeScript($job['revision'], 'postupdate');
                } elseif ($job['type'] == 'installClass') {
                    $status = Update::installClass($job['class']);
                } elseif ($job['type'] == 'cleanup') {
                    Update::cleanup();
                }
            }
        }

        $this->output->write(json_encode($status));
    }
}
