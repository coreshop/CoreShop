<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class SummaryCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'summary';
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //Nothing to do here
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //Nothing to do here
    }
}