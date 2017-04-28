<?php

namespace CoreShop\Bundle\CoreBundle\Product\Rule\Condition;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

class CustomersConditionChecker implements ConditionCheckerInterface
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
            $customer = $this->customerContext->getCustomer();

            return in_array($customer->getId(), $configuration['customers']);
        } catch (CustomerNotFoundException $ex) {

        }

        return false;
    }
}
