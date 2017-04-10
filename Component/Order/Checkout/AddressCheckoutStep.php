<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return $cart->getShippingAddress() instanceof AddressInterface && $cart->getInvoiceAddress() instanceof AddressInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //TODO: Implement Address Form Type and validate here
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //TODO Implement Address Form Type and return here
    }
}