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

namespace CoreShop\Bundle\RuleBundle\Assessor;

use Carbon\Carbon;
use CoreShop\Component\Rule\Condition\Assessor\RuleAvailabilityAssessorInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Repository\RuleRepositoryInterface;

final class RuleAvailabilityAssessor implements RuleAvailabilityAssessorInterface
{
    public function __construct(
        private RuleRepositoryInterface $ruleRepository,
    ) {
    }

    public function getRules(): array
    {
        return $this->ruleRepository->findActive();
    }

    public function isValid(RuleInterface $rule): bool
    {
        /** @var Condition $condition */
        foreach ($rule->getConditions() as $condition) {
            if ($condition->getType() !== 'timespan') {
                continue;
            }

            $configuration = $condition->getConfiguration();
            $dateFrom = Carbon::createFromTimestamp($configuration['dateFrom'] / 1000);
            $dateTo = Carbon::createFromTimestamp($configuration['dateTo'] / 1000);

            $date = Carbon::now();

            // future rule is also valid
            if ($configuration['dateFrom'] > 0) {
                if ($dateFrom->getTimestamp() > $date->getTimestamp()) {
                    return true;
                }
            }

            if ($configuration['dateFrom'] > 0) {
                if ($date->getTimestamp() < $dateFrom->getTimestamp()) {
                    return false;
                }
            }

            if ($configuration['dateTo'] > 0) {
                if ($date->getTimestamp() > $dateTo->getTimestamp()) {
                    return false;
                }
            }
        }

        return true;
    }
}
