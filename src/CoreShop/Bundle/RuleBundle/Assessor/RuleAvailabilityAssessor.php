<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\RuleBundle\Assessor;

use Carbon\Carbon;
use CoreShop\Component\Rule\Condition\Assessor\RuleAvailabilityAssessorInterface;
use CoreShop\Component\Rule\Model\Condition;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Repository\RuleRepositoryInterface;

final class RuleAvailabilityAssessor implements RuleAvailabilityAssessorInterface
{
    private RuleRepositoryInterface $ruleRepository;

    public function __construct(RuleRepositoryInterface $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }

    public function getRules(): array
    {
        return $this->ruleRepository->findActive();
    }

    public function isValid(RuleInterface $rule): bool
    {
        /** @var Condition $condition */
        foreach ($rule->getConditions() as $id => $condition) {
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
