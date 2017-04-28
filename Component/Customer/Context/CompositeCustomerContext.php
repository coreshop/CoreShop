<?php

namespace CoreShop\Component\Customer\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCustomerContext implements CustomerContextInterface
{
    /**
     * @var PriorityQueue|CustomerContextInterface[]
     */
    private $customerContexts;

    public function __construct()
    {
        $this->customerContexts = new PriorityQueue();
    }

    /**
     * @param CustomerContextInterface $customerContext
     * @param int $priority
     */
    public function addContext(CustomerContextInterface $customerContext, $priority = 0)
    {
        $this->customerContexts->insert($customerContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        foreach ($this->customerContexts as $customerContext) {
            try {
                return $customerContext->getCustomer();
            } catch (CustomerNotFoundException $exception) {
                continue;
            }
        }

        throw new CustomerNotFoundException();
    }
}
