<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Command;

use CoreShop\Component\StorageList\Expiration\StorageListExpirationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CartExpireCommand extends Command
{
    public function __construct(
        protected StorageListExpirationInterface $cartExpiration,
        protected array $params = [],
    ) {
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
                'Older than',
            )
            ->addOption(
                'anonymous',
                'a',
                InputOption::VALUE_NONE,
                'Delete only anonymous carts',
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_NONE,
                'Delete only user carts',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = $this->params['cart']['days'] ?? 0;
        $params = $this->params['cart']['params'] ?? [];

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
