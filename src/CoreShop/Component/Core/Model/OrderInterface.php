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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\OrderInterface as BaseOrderInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Shipping\Model\CarrierAwareInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface OrderInterface extends BaseOrderInterface, CarrierAwareInterface, PaymentSettingsAwareInterface, ShippableInterface
{
    /**
     * @return OrderItemInterface[]|null
     */
    public function getItems(): ?array;

    public function hasShippableItems(): ?bool;

    public function getWeight(): ?float;

    public function getShipping(bool $withTax = true): int;

    public function getShippingTax(): int;

    public function getShippingTaxRate(): ?float;

    public function setShippingTaxRate(?float $shippingTaxRate);

    public function getNeedsRecalculation(): ?bool;

    public function setNeedsRecalculation(?bool $needsRecalculation);
}
