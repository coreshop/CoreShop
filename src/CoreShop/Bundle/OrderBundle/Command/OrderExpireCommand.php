<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Command;

use CoreShop\Bundle\OrderBundle\Expiration\ProposalExpirationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class OrderExpireCommand extends Command
{
    protected $orderExpiration;
    protected $days;

    public function __construct(ProposalExpirationInterface $orderExpiration, int $days = 0)
    {
        parent::__construct();

        $this->orderExpiration = $orderExpiration;
        $this->days = $days;
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:order:expire')
            ->setDescription('Expire abandoned orders')
            ->addOption(
                'days',
                'days',
                InputOption::VALUE_OPTIONAL,
                'Older than'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = $this->days;

        if ($input->getOption('days')) {
            $days = (int) $input->getOption('days');
        }

        $output->writeln('Running order expire job, this could take some time.');

        $this->orderExpiration->expire($days);

        return 0;
    }
}
