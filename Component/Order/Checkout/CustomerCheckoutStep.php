<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        //TODO: check if we have a customer session

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //TODO: Check if we have a customer session
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //Nothing to do here
    }
}