<?php

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;

class Payment extends \Payum\Core\Model\Payment implements PaymentInterface
{
    use SetValuesTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var PaymentProviderInterface
     */
    protected $paymentProvider;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $state = PaymentInterface::STATE_NEW;

    /**
     * @var array
     */
    protected $details = [];

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var \DateTime
     */
    protected $datePayment;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider()
    {
        return $this->paymentProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider(PaymentProviderInterface $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        return $this->currency->getIsoCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatePayment()
    {
        return $this->datePayment;
    }

    /**
     * {@inheritdoc}
     */
    public function setDatePayment($datePayment)
    {
        $this->datePayment = $datePayment;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails($details)
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        if (!is_array($details)) {
            $details = [];
        }

        $this->details = $details;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }
}
