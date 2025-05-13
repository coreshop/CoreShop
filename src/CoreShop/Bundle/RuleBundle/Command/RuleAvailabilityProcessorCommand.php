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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\RuleBundle\Command;

use CoreShop\Bundle\RuleBundle\Processor\RuleAvailabilityProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RuleAvailabilityProcessorCommand extends Command
{
    public function __construct(
        private RuleAvailabilityProcessorInterface $ruleAvailabilityProcessor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:rules:check-availability')
            ->setDescription('Check for outdated / invalid rules and disable them.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ruleAvailabilityProcessor->process();

        return 0;
    }
}
