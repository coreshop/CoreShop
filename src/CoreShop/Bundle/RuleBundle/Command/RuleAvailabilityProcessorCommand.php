<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\RuleBundle\Command;

use CoreShop\Bundle\RuleBundle\Processor\RuleAvailabilityProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RuleAvailabilityProcessorCommand extends Command
{
    private RuleAvailabilityProcessorInterface $ruleAvailabilityProcessor;

    public function __construct(RuleAvailabilityProcessorInterface $ruleAvailabilityProcessor)
    {
        $this->ruleAvailabilityProcessor = $ruleAvailabilityProcessor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:rules:check-availability')
            ->setDescription('Check for outdated / invalid rules and disable them.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ruleAvailabilityProcessor->process();

        return 0;
    }
}
