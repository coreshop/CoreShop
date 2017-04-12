<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Bundle\PayumBundle\Model\PaymentProviderConfig;
use CoreShop\Component\Store\Model\StoresAwareInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface as BasePaymentProviderInterface;

interface PaymentProviderInterface extends BasePaymentProviderInterface, StoresAwareInterface
{
    /**
     * @param PaymentProviderConfig $paymentProviderConfig
     */
    public function setPaymentProviderConfig(PaymentProviderConfig $paymentProviderConfig);

    /**
     * @return PaymentProviderConfig
     */
    public function getPaymentProviderConfig();
}
