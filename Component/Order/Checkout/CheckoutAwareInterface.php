<?php

namespace CoreShop\Component\Order\Checkout;

interface CheckoutAwareInterface {

    /**
     * @return string
     */
    public function getCurrentStep();

    /**
     * @param string $name
     */
    public function setCurrentStep($name);

}