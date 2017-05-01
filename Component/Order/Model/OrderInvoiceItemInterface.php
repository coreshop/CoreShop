<?php

namespace CoreShop\Component\Order\Model;

interface OrderInvoiceItemInterface extends OrderDocumentItemInterface
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
    public function getTotalTax();

    /**
     * @param float $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes);
}