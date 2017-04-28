<?php

namespace CoreShop\Component\Customer\Context;

use CoreShop\Component\Customer\Model\CustomerInterface;

final class CachedCustomerContext implements CustomerContextInterface
{
    /**
     * @var CustomerInterface
     */
    private $customer = null;

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        if ($this->customer instanceof CustomerInterface) {
            return $this->customer;
        }

        throw new CustomerNotFoundException();
    }
}
