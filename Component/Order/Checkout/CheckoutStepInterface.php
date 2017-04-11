<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

interface CheckoutStepInterface {

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * Determines if this step should be forward if valid
     *
     * @return boolean
     */
    public function doAutoForward();

    /**
     * Check if Checkout Step is valid
     *
     * @param CartInterface $cart
     * @return boolean
     */
    public function validate(CartInterface $cart);

    /**
     * Commit Step from Request (validate form or whatever)
     *
     * @param CartInterface $cart
     * @param Request $request
     *
     * @throws CheckoutException
     */
    public function commitStep(CartInterface $cart, Request $request);

    /**
     * Prepare Checkout Step
     *
     * @param CartInterface $cart
     * @return array $params for the view
     */
    public function prepareStep(CartInterface $cart);
}