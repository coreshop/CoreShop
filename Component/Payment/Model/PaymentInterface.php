<?php

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Core\Model\CurrencyInterface;

interface PaymentInterface
{
    /**
     * @return mixed
     */
    public function getPaymentProvider();

    /**
     * @param $paymentProvider
     * @return mixed
     */
    public function setPaymentProvider($paymentProvider);

    /**
     * @return mixed
     */
    public function getState();

    /**
     * @param $state
     */
    public function setState($state);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency);

    /**
     * @return mixed
     */
    public function getDetails();

    /**
     * @param $details
     * @return mixed
     */
    public function setDetails($details);
}