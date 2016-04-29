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
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Tool\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Pimcore\Tool\Admin;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use CoreShop\Update;

class DeleteCartsCommand extends AbstractCommand
{
    /**
     * configure command
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:delete-carts')
            ->setDescription('Delete Carts')
            ->addOption(
                'days', 'days',
                InputOption::VALUE_OPTIONAL,
                "Older than"
            )
            ->addOption(
                'anonymous', 'a',
                InputOption::VALUE_NONE,
                'Delete only anonymous carts'
            )
            ->addOption(
                'user', 'u',
                InputOption::VALUE_NONE,
                'Delete only user carts'
            )
            ->addOption(
                'dry-run', 'd',
                InputOption::VALUE_NONE,
                'Dry-run'
            );
        ;
    }

    /**
     * execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption("dry-run");

        if($dryRun) {
            $output->writeLn("==========================================");
            $output->writeLn("DRY RUN");
            $output->writeLn("==========================================");
        }

        $conditions = array();
        $params = array();

        $list = new CoreShopCart\Listing();

        if($input->getOption("days")) {
            $daysTimestamp = new \Pimcore\Date();
            $daysTimestamp->addDay(-1 * $input->getOption("days"));

            $conditions[] = "o_creationDate < ?";
            $params[] = $daysTimestamp->getTimestamp();
        }

        if($input->getOption("anonymous")) {
            $conditions[] = "user__id IS NULL";
        }
        else if($input->getOption("user")) {
            $conditions[] = "user__id IS NOT NULL";
        }

        $list->setCondition(implode(" AND ", $conditions), $params);

        $carts = $list->load();

        if(count($carts) > 0) {
            $output->writeln("found " . count($carts) . " carts to delete");

            $progress = new ProgressBar($output, count($carts));
            $progress->start();

            foreach ($carts as $cart) {
                $progress->advance();

                if (!$dryRun) {
                    $cart->delete();
                }
            }

            $progress->finish();
        }
        else {
            $output->writeln("no carts found");
        }

        $output->writeLn("");
        $output->writeLn("finished");
    }
}