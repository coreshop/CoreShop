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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Console\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use CoreShop\Bundle\CoreShopLegacyBundle\Maintenance\CleanUpCart;

/**
 * Class DeleteCartsCommand
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Console\Command
 */
class DeleteCartsCommand extends AbstractCommand
{
    /**
     * execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');

        if ($dryRun) {
            $output->writeLn('==========================================');
            $output->writeLn('DRY RUN');
            $output->writeLn('==========================================');
        }

        $cleanUpParams = [];

        $days = $input->getOption('days');

        if (isset($days)) {
            $cleanUpParams['olderThanDays'] = (int) $input->getOption('days');
        }

        if ($input->getOption('anonymous')) {
            $cleanUpParams['deleteAnonymousCart'] = true;
        }
        if ($input->getOption('user')) {
            $cleanUpParams['deleteUserCart'] = true;
        }

        $cleanUpCart = new CleanUpCart();
        $cleanUpCart->setOptions($cleanUpParams);

        if ($cleanUpCart->hasErrors()) {
            foreach ($cleanUpCart->getErrors() as $error) {
                $this->output->writeln('<error>'.$error.'</error>');
            }

            return false;
        }

        $elements = $cleanUpCart->getCartElements();

        if (count($elements) > 0) {
            $output->writeln('found '.count($elements).' carts to delete.');

            $progress = new ProgressBar($output, count($elements));
            $progress->start();

            foreach ($elements as $cart) {
                $progress->advance();

                if (!$dryRun) {
                    $cleanUpCart->deleteCart($cart);
                }
            }

            $progress->finish();
            $output->writeLn("\nCleanUp finished.");
        } else {
            $output->writeln('No carts found.');
        }

        return 0;
    }

    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:cleanup:carts')
            ->setDescription('Cleanup Carts')
            ->addOption(
                'days', 'days',
                InputOption::VALUE_OPTIONAL,
                'Older than'
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
    }
}
