<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Component\Store\Model\StoresAwareInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface as BasePaymentProviderInterface;

interface PaymentProviderInterface extends BasePaymentProviderInterface, StoresAwareInterface
{
    /**
     * @param GatewayConfig $gatewayConfig
     */
    public function setGatewayConfig(GatewayConfig $gatewayConfig);

    /**
     * @return GatewayConfig
     */
    public function getGatewayConfig();
}
