<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Maintenance;

use CoreShop\Bundle\RuleBundle\Processor\RuleAvailabilityProcessorInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Pimcore\Maintenance\TaskInterface;

final class RuleAvailabilityTask implements TaskInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var RuleAvailabilityProcessorInterface
     */
    private $ruleAvailabilityProcessor;

    /**
     * @param ConfigurationServiceInterface      $configurationService
     * @param RuleAvailabilityProcessorInterface $ruleAvailabilityProcessor
     */
    public function __construct(
        ConfigurationServiceInterface $configurationService,
        RuleAvailabilityProcessorInterface $ruleAvailabilityProcessor
    ) {
        $this->configurationService = $configurationService;
        $this->ruleAvailabilityProcessor = $ruleAvailabilityProcessor;
    }

    public function execute()
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
