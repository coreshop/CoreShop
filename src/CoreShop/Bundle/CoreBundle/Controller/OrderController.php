<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    /**
     * {@inheritdoc}
     */
    protected function prepareSale(OrderInterface $sale): array
    {
        $order = parent::prepareSale($sale);

        if ($sale instanceof CoreOrderInterface) {
            $order['carrier'] = $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getId() : null;
            $order['shipping'] = $sale->getShipping();
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails(OrderInterface $sale): array
    {
        $json = parent::getDetails($sale);

        if ($sale instanceof CoreOrderInterface) {
            $json['shippingPayment'] = [
                'carrier' => $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getIdentifier() : null,
                'weight' => $sale->getWeight(),
                'cost' => $sale->getShipping(),
            ];

            if ($sale->getCarrier()) {
                $json['carrierInfo'] = [
                    'name' => $sale->getCarrier()->getTitle(),
                ];
            }
        }

        return $json;
    }

    protected function getSummary(OrderInterface $order): array
    {
        $summary = parent::getSummary($order);

        if ($order instanceof \CoreShop\Component\Core\Model\OrderInterface && $order->getShipping() > 0) {
            $summary[] = [
                'key' => 'shipping',
                'value' => $order->getShipping(),
                'convertedValue' => $order->getConvertedShipping(),
            ];

            $summary[] = [
                'key' => 'shipping_tax',
                'value' => $order->getShippingTax(),
                'convertedValue' => $order->getConvertedShippingTax(),
            ];
        }

        return $summary;
    }

    protected function prepareSaleItem(OrderItemInterface $item): array
    {
        $itemData = parent::prepareSaleItem($item);

        /**
         * @var CoreOrderItemInterface $item
         */
        if (!$item instanceof CoreOrderItemInterface) {
            return $itemData;
        }

        if (!is_string($item->getUnitIdentifier())) {
            return $itemData;
        }

        $itemData['unit'] = $item->hasUnitDefinition() ? $item->getUnitDefinition()->getUnit()->getFullLabel() : $item->getUnitIdentifier();

        return $itemData;
    }
}
