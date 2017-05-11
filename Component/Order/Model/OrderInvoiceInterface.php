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
     * @return float
     */
    public function getDiscount($withTax = true);

    /**
     * @param float $discount
     * @param bool  $withTax
     */
    public function setDiscount($discount, $withTax = true);

    /**
     * @return float
     */
    public function getDiscountTax();

    /**
     * @param float $discountTax
     */
    public function setDiscountTax($discountTax);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getShipping($withTax = true);

    /**
     * @param float $shipping
     * @param bool  $withTax
     */
    public function setShipping($shipping, $withTax = true);

    /**
     * @return float
     */
    public function getShippingTaxRate();

    /**
     * @param float $shippingTaxRate
     */
    public function setShippingTaxRate($shippingTaxRate);

    /**
     * @return float
     */
    public function getShippingTax();

    /**
     * @param float $shippingTax
     */
    public function setShippingTax($shippingTax);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getPaymentFee($withTax = true);

    /**
     * @param float $paymentFee
     * @param bool  $withTax
     */
    public function setPaymentFee($paymentFee, $withTax = true);

    /**
     * @return mixed
     */
    public function getPaymentFeeTaxRate();

    /**
     * @param float $paymentFeeTaxRate
     */
    public function setPaymentFeeTaxRate($paymentFeeTaxRate);

    /**
     * @return mixed
     */
    public function getPaymentFeeTax();

    /**
     * @param float $paymentFeeTax
     */
    public function setPaymentFeeTax($paymentFeeTax);

    /**
     * @return float
     */
    public function getTotalTax();

    /**
     * @param float $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @param bool  $withTax
     * @param float $total
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return float
     */
    public function getSubtotalTax();

    /**
     * @param float $subtotalTax
     */
    public function setSubtotalTax($subtotalTax);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getSubtotal($withTax = true);

    /**
     * @param float $subtotal
     * @param bool  $withTax
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
}
