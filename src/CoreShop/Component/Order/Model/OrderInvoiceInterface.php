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

namespace CoreShop\Component\Order\Model;

interface OrderInvoiceInterface extends OrderDocumentInterface, AdjustableInterface, BaseAdjustableInterface
{
    /**
     * @return \DateTime
     */
    public function getInvoiceDate();

    /**
     * @param \DateTime $invoiceDate
     */
    public function setInvoiceDate($invoiceDate);

    /**
     * @return string
     */
    public function getInvoiceNumber();

    /**
     * @param string $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber);

    /**
     * @return mixed
     */
    public function getPriceRuleItems();

    /**
     * @param mixed $priceRules
     */
    public function setPriceRuleItems($priceRules);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getDiscount($withTax = true);

    /**
     * @return int
     */
    public function getDiscountTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getShipping($withTax = true);

    /**
     * @return int
     */
    public function getShippingTaxRate();

    /**
     * @param int $shippingTaxRate
     */
    public function setShippingTaxRate($shippingTaxRate);

    /**
     * @return int
     */
    public function getShippingTax();

    /**
     * @return int
     */
    public function getTotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal($withTax = true);

    /**
     * @param bool $withTax
     * @param int  $total
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return int
     */
    public function getSubtotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getSubtotal($withTax = true);

    /**
     * @param int  $subtotal
     * @param bool $withTax
     */
    public function setSubtotal($subtotal, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseDiscount($withTax = true);

    /**
     * @return int
     */
    public function getBaseDiscountTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseShipping($withTax = true);

    /**
     * @return int
     */
    public function getBaseShippingTax();

    /**
     * @return int
     */
    public function getBaseTotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param int  $baseTotal
     * @param bool $withTax
     */
    public function setBaseTotal($baseTotal, $withTax = true);

    /**
     * @return int
     */
    public function getBaseSubtotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseSubtotal($withTax = true);

    /**
     * @param int  $baseSubtotal
     * @param bool $withTax
     */
    public function setBaseSubtotal($baseSubtotal, $withTax = true);
}
