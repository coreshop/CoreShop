<?php

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface PaymentInterface extends \Payum\Core\Model\PaymentInterface, PimcoreModelInterface
{
    const STATE_NEW = 'new';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETED = 'completed';
    const STATE_FAILED = 'failed';
    const STATE_CANCELLED = 'cancelled';
    const STATE_REFUNDED = 'refunded';
    const STATE_UNKNOWN = 'unknown';

    /**
     * @return PaymentProviderInterface
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
    public function getDatePayment();

    /**
     * @param $datePayment
     */
    public function setDatePayment($datePayment);

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

     /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @param $number
     */
    public function setNumber($number);
}