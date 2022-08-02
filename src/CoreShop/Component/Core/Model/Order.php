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

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\Order as BaseOrder;
use CoreShop\Component\Shipping\Model\CarrierAwareTrait;

abstract class Order extends BaseOrder implements OrderInterface
{
    use CarrierAwareTrait;

    public function getShipping(bool $withTax = true): int
    {
        return $withTax ? $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, true) : $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, false);
    }

    public function getShippingTax(): int
    {
        return $this->getShipping(true) - $this->getShipping(false);
    }

    public function hasShippableItems(): ?bool
    {
        $shippable = false;
        /** @var OrderItemInterface $item */
        foreach ($this->getItems() as $item) {
            if ($item->getDigitalProduct() !== true) {
                $shippable = true;

                break;
            }
        }

        return $shippable;
    }
}
