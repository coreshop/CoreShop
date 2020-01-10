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

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class CustomerGroupsConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, $params = [])
    {
        if (!array_key_exists('customer', $params) || !$params['customer'] instanceof CustomerInterface) {
            return false;
        }

        /**
         * @var CustomerInterface $customer
         */
        $customer = $params['customer'];

        foreach ($customer->getCustomerGroups() as $group) {
            if ($group instanceof ResourceInterface) {
                if (in_array($group->getId(), $configuration['customerGroups'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
