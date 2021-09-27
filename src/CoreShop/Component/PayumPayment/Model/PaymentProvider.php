<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\PayumPayment\Model;

use CoreShop\Component\Payment\Model\PaymentProvider as BasePaymentProvider;

/**
 * @psalm-suppress MissingConstructor
 */
class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface
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
