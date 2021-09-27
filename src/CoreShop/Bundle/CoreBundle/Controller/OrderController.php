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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\OrderBundle\Controller\OrderController as BaseOrderController;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\OrderInterface as CoreOrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface as CoreOrderItemInterface;

class OrderController extends BaseOrderController
{
    protected function prepareSale(OrderInterface $order): array
    {
        $serialized = parent::prepareSale($order);

        if ($order instanceof CoreOrderInterface) {
            $serialized['carrier'] = $order->getCarrier() instanceof CarrierInterface ? $order->getCarrier()->getId() : null;
            $serialized['shipping'] = $order->getShipping();
        }

        return $serialized;
    }

    protected function getDetails(OrderInterface $order): array
    {
        $serialized = parent::getDetails($order);

        if ($order instanceof CoreOrderInterface) {
            $serialized['shippingPayment'] = [
                'carrier' => $order->getCarrier() instanceof CarrierInterface ? $order->getCarrier()->getIdentifier() : null,
                'weight' => $order->getWeight(),
                'cost' => $order->getShipping(),
            ];

            if ($order->getCarrier()) {
                $serialized['carrierInfo'] = [
                    'name' => $order->getCarrier()->getTitle(),
                ];
            }
        }

        return $serialized;
    }

    protected function getSummary(OrderInterface $order): array
    {
        $serialized = parent::getSummary($order);

        if ($order instanceof CoreOrderInterface && $order->getShipping() > 0) {
            $serialized[] = [
                'key' => 'shipping',
                'value' => $order->getShipping(),
                'convertedValue' => $order->getConvertedShipping(),
            ];

            $serialized[] = [
                'key' => 'shipping_tax',
                'value' => $order->getShippingTax(),
                'convertedValue' => $order->getConvertedShippingTax(),
            ];
        }

        return $serialized;
    }

    protected function prepareSaleItem(OrderItemInterface $item): array
    {
        $serialized = parent::prepareSaleItem($item);

        if (!$item instanceof CoreOrderItemInterface) {
            return $serialized;
        }

        if (!is_string($item->getUnitIdentifier())) {
            return $serialized;
        }

        $serialized['unit'] = $item->hasUnitDefinition() ? $item->getUnitDefinition()->getUnit()->getFullLabel() : $item->getUnitIdentifier();

        return $serialized;
    }
}
