<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\OrderBundle\Controller\OrderController as BaseOrderController;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\SaleInterface;

class OrderController extends BaseOrderController
{
    /**
     * {@inheritdoc}
     */
    protected function prepareSale(SaleInterface $sale)
    {
        $order = parent::prepareSale($sale);

        if ($sale instanceof OrderInterface) {
            $order['carrier'] = $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getId() : null;
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails(SaleInterface $sale)
    {
        $order = parent::getDetails($sale);

        if ($sale instanceof OrderInterface) {
            $order['shippingPayment'] = [
                'carrier' => $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getIdentifier() : null,
                'weight' => $sale->getWeight(),
                'cost' => $sale->getShipping(),
            ];
        }

        return $order;
    }
}
