<?php

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerRegistrationEvent extends GenericEvent
{
    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var array
     */
    private $data;

    /**
     * @param CustomerInterface $customer
     * @param array             $data
     */
    public function __construct(CustomerInterface $customer, array $data)
    {
        parent::__construct($customer);

        $this->customer = $customer;
        $this->data = $data;
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
