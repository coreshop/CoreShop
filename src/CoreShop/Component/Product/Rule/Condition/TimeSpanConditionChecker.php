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

namespace CoreShop\Component\Product\Rule\Condition;

use Carbon\Carbon;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

class TimeSpanConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, $params = [])
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        $dateFrom = Carbon::createFromTimestamp($configuration['dateFrom'] / 1000);
        $dateTo = Carbon::createFromTimestamp($configuration['dateTo'] / 1000);

        $date = Carbon::now();

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

        return true;
    }
}
