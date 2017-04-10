<?php

namespace CoreShop\Component\Order\Checkout;

class CheckoutException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($reason)
    {
        parent::__construct($reason);
    }
}
