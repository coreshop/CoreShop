<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CartCleanupCommand extends ContainerAwareCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:cart:cleanup')
            ->setDescription('Cleanup abandoned Carts')
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
            );
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cleanupTask = $this->getContainer()->get('coreshop.cart.cleanup');

        if ($input->getOption('days')) {
            $cleanupTask->setExpirationDays((int)$input->getOption('days'));
        }

        if ($input->getOption('anonymous')) {
            $cleanupTask->setCleanupAnonymous(true);
        }
        if ($input->getOption('user')) {
            $cleanupTask->setCleanupUser(true);
        }

        $output->writeln('Running cleanup job, this could take some time.');

        $cleanupTask->cleanup();

        return 0;
    }
}
