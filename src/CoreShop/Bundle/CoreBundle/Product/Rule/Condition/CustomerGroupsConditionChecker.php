<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\CoreBundle\Product\Rule\Condition;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

final class CustomerGroupsConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        try {
            /**
             * @var CustomerInterface
             */
            $customer = $this->customerContext->getCustomer();

            foreach ($customer->getCustomerGroups() as $group) {
                if ($group instanceof ResourceInterface) {
                    if (in_array($group->getId(), $configuration['customerGroups'])) {
                        return true;
                    }
                }
            }
        } catch (CustomerNotFoundException $ex) {
            //If some goes wrong, we just ignore it and return false
        }

        return false;
    }
}
