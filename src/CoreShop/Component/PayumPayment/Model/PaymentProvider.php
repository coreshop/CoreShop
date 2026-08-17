<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\PayumPayment\Model;

use CoreShop\Component\Payment\Model\PaymentProvider as BasePaymentProvider;

/**
 * @psalm-suppress MissingConstructor
 */
class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface, \Stringable
{
    protected ?GatewayConfig $gatewayConfig = null;

    public function setGatewayConfig(GatewayConfig $gatewayConfig): void
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    public function getGatewayConfig(): ?GatewayConfig
    {
        return $this->gatewayConfig;
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->getIdentifier());
    }
}
