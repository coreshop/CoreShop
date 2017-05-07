<?php

namespace CoreShop\Component\Order\Model;

interface OrderShipmentItemInterface extends OrderDocumentItemInterface
{
    /**
     * @param bool $withTax
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @param float $total
     * @param boolean $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $weight
     */
    public function setWeight($weight);
}