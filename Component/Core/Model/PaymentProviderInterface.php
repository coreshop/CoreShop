<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Component\Payment\Model\PaymentProviderInterface as BasePaymentProviderInterface;
use CoreShop\Component\Store\Model\StoresAwareInterface;

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
