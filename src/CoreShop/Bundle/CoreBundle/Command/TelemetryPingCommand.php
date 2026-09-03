<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Component\Core\Telemetry\TelemetryPingerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TelemetryPingCommand extends Command
{
    public function __construct(
        private readonly TelemetryPingerInterface $pinger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:telemetry:ping')
            ->setDescription('Sends the CoreShop telemetry ping to the license portal now.')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Print the payload that would be sent, without sending it.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->pinger->isEnabled()) {
            $io->note('Telemetry is disabled (CORESHOP_TELEMETRY / core_shop_core.telemetry.enabled).');
        }

        if ($input->getOption('dump')) {
            $io->writeln(json_encode($this->pinger->buildPayload(), \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        }

        if (!$this->pinger->isEnabled()) {
            return Command::SUCCESS;
        }

        $response = $this->pinger->ping();

        if (null === $response) {
            $io->warning('Ping failed, see the application log for details.');

            return Command::SUCCESS;
        }

        $io->success('Ping sent.');
        $io->writeln(json_encode($response, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
