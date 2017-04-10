<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class SummaryCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        //This step should never return false, should it?
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //TODO Implement Summary Form Type and validate here
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //TODO Implement Address Form Type and return here
    }
}