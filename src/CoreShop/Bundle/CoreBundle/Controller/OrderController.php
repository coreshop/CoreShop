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
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Store\Model\StoreInterface;
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
            $order['store'] = $sale->getStore() instanceof StoreInterface ? $sale->getStore()->getId() : null;
        }

        return $order;
    }

    protected function getDetails(SaleInterface $sale)
    {
        $order = parent::getDetails($sale);

        if ($sale instanceof OrderInterface) {
            $order['shippingPayment'] = [
                'carrier' => $sale->getCarrier() instanceof CarrierInterface ? $sale->getCarrier()->getName() : null,
                'weight' => $sale->getWeight(),
                'cost' => $sale->getShipping(),
            ];
            $order['store'] = $sale->getStore() instanceof StoreInterface ? $this->getStore($sale->getStore()) : null;
            $order['invoiceCreationAllowed'] = !$this->getInvoiceProcessableHelper()->isFullyProcessed($sale) && count($sale->getPayments()) !== 0;
            $order['shipmentCreationAllowed'] = !$this->getShipmentProcessableHelper()->isFullyProcessed($sale) && count($sale->getPayments()) !== 0;

        }

        return $order;
    }

    protected function getStore(StoreInterface $store) {
        return [
            "id" => $store->getId(),
            "name" => $store->getName()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getGridColumns()
    {
        $columns = parent::getGridColumns();

        $columns[] = [
            'text' => 'coreshop_store',
            'type' => 'integer',
            'dataIndex' => 'store',
            'renderAs' => 'store',
            'filter' => [
                'type' => 'number',
            ]
        ];

        return $columns;
    }

    /**
     * @return ProcessableInterface
     */
    private function getInvoiceProcessableHelper()
    {
        return $this->get('coreshop.order.invoice.processable');
    }
    /**
     * @return ProcessableInterface
     */
    private function getShipmentProcessableHelper()
    {
        return $this->get('coreshop.order.shipment.processable');
    }
}