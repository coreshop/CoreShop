<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        //TODO: Implement Payment Stuff
        //return $cart->getPaymentProvider();

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //TODO: Implement Payment Form Type and validate here
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //TODO Implement Payment Form Type and return here
    }
}