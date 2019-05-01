<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\OrderBundle\Controller\CartController as BaseCartController;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;

class CartController extends BaseCartController
{
    /**
     * {@inheritdoc}
     */
    protected function prepareSale(CartInterface $sale)
    {
        $order = parent::prepareSale($sale);

        if ($sale instanceof \CoreShop\Component\Core\Model\CartInterface) {
            $order['carrier'] = $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getId() : null;
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails(CartInterface $sale)
    {
        $order = parent::getDetails($sale);

        if ($sale instanceof \CoreShop\Component\Core\Model\CartInterface) {
            $order['shippingPayment'] = [
                'carrier' => $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getIdentifier() : null,
                'weight'  => $sale->getWeight(),
                'cost'    => $sale->getShipping(),
            ];

            if ($sale->getCarrier() instanceof CarrierInterface) {
                $order['carrierInfo'] = [
                    'name' => $sale->getCarrier()->getTitle()
                ];
            }
        }

        return $order;
    }

    protected function getSummary(CartInterface $cart)
    {
        $summary = parent::getSummary($cart);

        if ($cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
            if ($cart->getShipping() > 0) {
                $summary[] = [
                    'key' => 'shipping',
                    'value' => $cart->getShipping(),
                ];

                $summary[] = [
                    'key' => 'shipping_tax',
                    'value' => $cart->getShippingTax(),
                ];
            }
        }

        return $summary;
    }

    /**
     * @param CartItemInterface $item
     *
     * @return array
     */
    protected function prepareSaleItem(CartItemInterface $item)
    {
        $itemData = parent::prepareCartItem($item);

        /*if (!$item instanceof CartItemInterface) {
            return $itemData;
        }

        if (!is_string($item->getUnitDefinition())) {
            return $itemData;
        }

        $itemData['unit'] = $item->hasUnit() ? $item->getUnit()->getFullLabel() : $item->getUnitIdentifier();
*/
        return $itemData;
    }
}
