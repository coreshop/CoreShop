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

namespace CoreShop\Component\Order\Model;

interface OrderInvoiceInterface extends OrderDocumentInterface
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
     * @param int $discount
     * @param bool $withTax
     */
    public function setDiscount($discount, $withTax = true);

    /**
     * @return int
     */
    public function getDiscountTax();

    /**
     * @param int $discountTax
     */
    public function setDiscountTax($discountTax);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getShipping($withTax = true);

    /**
     * @param int $shipping
     * @param bool $withTax
     */
    public function setShipping($shipping, $withTax = true);

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
     * @param int $shippingTax
     */
    public function setShippingTax($shippingTax);

    /**
     * @return int
     */
    public function getTotalTax();

    /**
     * @param int $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal($withTax = true);

    /**
     * @param bool $withTax
     * @param int $total
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return int
     */
    public function getSubtotalTax();

    /**
     * @param int $subtotalTax
     */
    public function setSubtotalTax($subtotalTax);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getSubtotal($withTax = true);

    /**
     * @param int $subtotal
     * @param bool $withTax
     */
    public function setSubtotal($subtotal, $withTax = true);

    /**
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseDiscount($withTax = true);

    /**
     * @param int $baseDiscount
     * @param bool $withTax
     */
    public function setBaseDiscount($baseDiscount, $withTax = true);

    /**
     * @return int
     */
    public function getBaseDiscountTax();

    /**
     * @param int $baseDiscountTax
     */
    public function setBaseDiscountTax($baseDiscountTax);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseShipping($withTax = true);

    /**
     * @param int $baseShipping
     * @param bool $withTax
     */
    public function setBaseShipping($baseShipping, $withTax = true);

    /**
     * @return int
     */
    public function getBaseShippingTax();

    /**
     * @param int $baseShippingTax
     */
    public function setBaseShippingTax($baseShippingTax);

    /**
     * @return int
     */
    public function getBaseTotalTax();

    /**
     * @param int $baseTotalTax
     */
    public function setBaseTotalTax($baseTotalTax);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param int $baseTotal
     * @param bool $withTax
     */
    public function setBaseTotal($baseTotal, $withTax = true);

    /**
     * @return int
     */
    public function getBaseSubtotalTax();

    /**
     * @param int $baseSubtotalTax
     */
    public function setBaseSubtotalTax($baseSubtotalTax);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseSubtotal($withTax = true);

    /**
     * @param int $baseSubtotal
     * @param bool $withTax
     */
    public function setBaseSubtotal($baseSubtotal, $withTax = true);

    /**
     * @return mixed
     */
    public function getBaseTaxes();

    /**
     * @param mixed $baseTaxes
     */
    public function setBaseTaxes($baseTaxes);
}
