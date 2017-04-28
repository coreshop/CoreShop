<?php

namespace CoreShop\Bundle\CoreBundle\Product\Rule\Condition;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

class CustomerGroupsConditionChecker implements ConditionCheckerInterface
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
             * @var $customer CustomerInterface
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

        }

        return false;
    }
}
