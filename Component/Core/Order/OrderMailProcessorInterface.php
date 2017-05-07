<?php

namespace CoreShop\Component\Core\Order;

use CoreShop\Component\Order\Model\OrderInterface;

interface OrderMailProcessorInterface
{
    /**
     * @param $emailDocument
     * @param OrderInterface $order
     * @param bool $sendInvoices
     * @param bool $sendShipments
     * @param array $params
     */
    public function sendOrderMail($emailDocument, OrderInterface $order, $sendInvoices = false, $sendShipments = false, $params = []);
}