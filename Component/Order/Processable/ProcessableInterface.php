<?php

namespace CoreShop\Component\Order\Processable;

use CoreShop\Component\Order\Model\OrderInterface;

interface ProcessableInterface
{
    /**
     * @param OrderInterface $order
     * @return mixed
     */
    public function getProcessableItems(OrderInterface $order);

    /**
     * @param OrderInterface $order
     * @return mixed
     */
    public function getProcessedItems(OrderInterface $order);

    /**
     * @param OrderInterface $order
     * @return boolean
     */
    public function isFullyProcessed(OrderInterface $order);
}