<?php

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Condition;

use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CustomerGroupsConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        /**
         * @var $subject CartInterface
         */
        Assert::isInstanceOf($subject, CartInterface::class);

        if (!$subject->getCustomer() instanceof CustomerInterface) {
            return false;
        }

        foreach ($subject->getCustomer()->getCustomerGroups() as $customerGroup) {
            if ($customerGroup instanceof CustomerGroupInterface) {
                if (in_array($customerGroup->getId(), $configuration['customerGroups'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
