<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class CartCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return count($cart->getItems()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //nothing to do here
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //nothing to do here
    }
}