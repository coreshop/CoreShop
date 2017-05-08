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
 *
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
     *
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
     * @param mixed$priceRules
     */
    public function setPriceRuleItems($priceRules);

    /**
     * @param boolean $withTax
     * @return double
     */
    public function getDiscount($withTax = true);

    /**
     * @param double $discount
     * @param boolean $withTax
     */
    public function setDiscount($discount, $withTax = true);

    /**
     * @return double
     */
    public function getDiscountTax();

    /**
     * @param double $discountTax
     */
    public function setDiscountTax($discountTax);

    /**
     * @param boolean $withTax
     * @return double
     */
    public function getShipping($withTax = true);

    /**
     * @param double $shipping
     * @param boolean $withTax
     */
    public function setShipping($shipping, $withTax = true);

    /**
     * @return double
     */
    public function getShippingTaxRate();

    /**
     * @param double $shippingTaxRate
     */
    public function setShippingTaxRate($shippingTaxRate);

    /**
     * @return double
     */
    public function getShippingTax();

    /**
     * @param double $shippingTax
     */
    public function setShippingTax($shippingTax);

    /**
     * @param boolean $withTax
     * @return double
     */
    public function getPaymentFee($withTax = true);

    /**
     * @param double $paymentFee
     * @param boolean $withTax
     */
    public function setPaymentFee($paymentFee, $withTax = true);

    /**
     * @return mixed
     */
    public function getPaymentFeeTaxRate();

    /**
     * @param double $paymentFeeTaxRate
     */
    public function setPaymentFeeTaxRate($paymentFeeTaxRate);

    /**
     * @return mixed
     */
    public function getPaymentFeeTax();

    /**
     * @param double $paymentFeeTax
     */
    public function setPaymentFeeTax($paymentFeeTax);

    /**
     * @return double
     */
    public function getTotalTax();

    /**
     * @param double $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @param boolean $withTax
     * @return double
     */
    public function getTotal($withTax = true);

    /**
     * @param boolean $withTax
     * @param double $total
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return double
     */
    public function getSubtotalTax();

    /**
     * @param double $subtotalTax
     */
    public function setSubtotalTax($subtotalTax);

    /**
     * @param boolean $withTax
     * @return double
     */
    public function getSubtotal($withTax = true);

    /**
     * @param double $subtotal
     * @param boolean $withTax
     */
    public function setSubtotal($subtotal, $withTax = true);

    /**
     * @return mixed
     *
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     *
     */
    public function setTaxes($taxes);
}