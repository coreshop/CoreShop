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

use CoreShop\Bundle\OrderBundle\Expiration\OrderExpirationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CartExpireCommand extends Command
{
    protected OrderExpirationInterface $cartExpiration;
    protected int $days;
    protected array $params;

    public function __construct(OrderExpirationInterface $cartExpiration, int $days = 0, array $params = [])
    {
        $this->cartExpiration = $cartExpiration;
        $this->days = $days;
        $this->params = $params;

        parent::__construct();
    }

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
            )
            ->addOption(
                'anonymous',
                'a',
                InputOption::VALUE_NONE,
                'Delete only anonymous carts'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_NONE,
                'Delete only user carts'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = $this->days;
        $params = $this->params;

        if ($input->getOption('days')) {
            $days = (int) $input->getOption('days');
        }

        if ($input->getOption('anonymous')) {
            $params['anonymous'] = true;
        }
        if ($input->getOption('user')) {
            $params['user'] = true;
        }

        $output->writeln('Running cart expire job, this could take some time.');

        $this->cartExpiration->expire($days, $params);

        return 0;
    }
}
