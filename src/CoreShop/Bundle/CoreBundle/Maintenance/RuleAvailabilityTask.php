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

namespace CoreShop\Bundle\CoreBundle\Maintenance;

use CoreShop\Bundle\RuleBundle\Processor\RuleAvailabilityProcessorInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Pimcore\Maintenance\TaskInterface;

final class RuleAvailabilityTask implements TaskInterface
{
    public function __construct(
        private ConfigurationServiceInterface $configurationService,
        private RuleAvailabilityProcessorInterface $ruleAvailabilityProcessor,
    ) {
    }

    public function execute(): void
    {
        $lastMaintenance = $this->configurationService->get('system.rule.availability_check.last_run');

        if (null === $lastMaintenance) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $this->ruleAvailabilityProcessor->process();
            $this->configurationService->set('system.rule.availability_check.last_run', time());
        }
    }
}
