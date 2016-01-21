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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CoreShop\Update;

class InternalUpdateProcessorCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('coreshop:internal:update-processor')
            ->setDescription('For internal use only')
            ->addArgument("config");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $status = ["success" => true];
        $config = $input->getArgument("config");

        if($config) {
            $job = json_decode($config, true);

            if(is_array($job)) {

                if (isset($job["dry-run"])) {
                    // do not do anything here
                    \Logger::info("skipped update job because it is in dry-run mode", $job);
                } else if ($job["type"] == "deleteFile") {
                    Update::deleteData($job["url"]);
                } else if ($job["type"] == "files") {
                    Update::installData($job["revision"]);
                } else if ($job["type"] == "clearcache") {
                    \Pimcore\Cache::clearAll();
                } else if ($job["type"] == "preupdate") {
                    $status = Update::executeScript($job["revision"], "preupdate");
                } else if ($job["type"] == "postupdate") {
                    $status = Update::executeScript($job["revision"], "postupdate");
                } else if ($this->getParam("type") == "installClass") {
                    $status = Update::installClass($job["class"]);
                }else if ($job["type"] == "cleanup") {
                    Update::cleanup();
                }
            }
        }

        $this->output->write(json_encode($status));
    }
}
