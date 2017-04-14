<?php

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface PaymentInterface extends \Payum\Core\Model\PaymentInterface, ResourceInterface
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
    public function setPaymentProvider(PaymentProviderInterface $paymentProvider);

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
     * @param int $amount
     */
    public function setTotalAmount($amount);

    /**
     * @param $number
     */
    public function setNumber($number);

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getOrderId();
}