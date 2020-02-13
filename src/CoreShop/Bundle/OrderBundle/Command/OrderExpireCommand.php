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

namespace CoreShop\Bundle\OrderBundle\Command;

use CoreShop\Bundle\OrderBundle\Expiration\ProposalExpirationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class OrderExpireCommand extends Command
{
    /**
     * @var ProposalExpirationInterface
     */
    protected $orderExpiration;

    /**
     * @var int
     */
    protected $days;

    /**
     * @param ProposalExpirationInterface $orderExpiration
     * @param int                         $days
     */
    public function __construct(ProposalExpirationInterface $orderExpiration, $days = 0)
    {
        parent::__construct();

        $this->orderExpiration = $orderExpiration;
        $this->days = $days;
    }

    /**
     * configure command.
     */
    protected function configure(): void
    {
        $this
            ->setName('coreshop:cart:expire')
            ->setDescription('Expire abandoned Carts')
            ->addOption(
                'days',
                'days',
                InputOption::VALUE_OPTIONAL,
                'Older than'
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
        $days = $this->days;

        if ($input->getOption('days')) {
            $days = (int) $input->getOption('days');
        }

        $output->writeln('Running order expire job, this could take some time.');

        $this->orderExpiration->expire($days);

        return 0;
    }
}
