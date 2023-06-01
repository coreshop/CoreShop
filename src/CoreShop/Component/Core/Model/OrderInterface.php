<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\OrderInterface as BaseOrderInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Shipping\Model\CarrierAwareInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface OrderInterface extends
    BaseOrderInterface,
    CarrierAwareInterface,
    PaymentSettingsAwareInterface,
    ShippableInterface
{
    /**
     * @return OrderItemInterface[]|null
     */
    public function getItems(): ?array;

    public function hasShippableItems(): ?bool;

    public function getWeight(): ?float;

    public function getPaymentProviderFee(): int;

    public function getShipping(bool $withTax = true): int;

    public function getShippingTax(): int;

    public function getShippingTaxRate(): ?float;

    public function setShippingTaxRate(?float $shippingTaxRate);

    public function getNeedsRecalculation(): ?bool;

    public function setNeedsRecalculation(?bool $needsRecalculation);
}
