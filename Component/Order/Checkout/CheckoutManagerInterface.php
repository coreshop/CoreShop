<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

interface CheckoutManagerInterface {

    /**
     * @return mixed
     */
    public function getSteps();

    /**
     * @param $identifier
     * @return mixed
     */
    public function getStep($identifier);

    /**
     * @param $identifier
     * @return mixed
     */
    public function getNextStep($identifier);

    /**
     * @param $identifier
     * @return mixed
     */
    public function getPreviousStep($identifier);

    /**
     * @param $identifier
     * @return CheckoutStepInterface[]
     */
    public function getPreviousSteps($identifier);

    /**
     * @param CheckoutStepInterface $step
     * @param CartInterface $cart
     * @return mixed
     */
    public function validateStep(CheckoutStepInterface $step, CartInterface $cart);

    /**
     * @param CheckoutStepInterface $step
     * @param CartInterface $cart
     * @return mixed
     */
    public function prepareStep(CheckoutStepInterface $step, CartInterface $cart);

    /**
     * @param CartInterface $cart
     * @return mixed
     */
    public function getCurrentStep(CartInterface $cart);

    /**
     * @param $identifier
     * @return mixed
     */
    public function getCurrentStepIndex($identifier);

    /**
     * @param CheckoutStepInterface $step
     * @param CartInterface $cart
     * @param Request $request
     * @return mixed
     */
    public function commitStep(CheckoutStepInterface $step, CartInterface $cart, Request $request);
}