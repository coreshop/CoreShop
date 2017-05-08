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

use Carbon\Carbon;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface OrderInterface extends ProposalInterface, PimcoreModelInterface
{
    /**
     * @return StoreInterface
     */
    public function getStore();

    /**
     * @param StoreInterface $store
     *
     * @return static
     */
    public function setStore($store);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     *
     * @return static
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getOrderLanguage();

    /**
     * @param $orderLanguage
     */
    public function setOrderLanguage($orderLanguage);

    /**
     * @param $total
     * @param bool $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @param $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @param $subtotal
     * @param bool $withTax
     */
    public function setSubtotal($subtotal, $withTax = true);

    /**
     * @param $subtotalTax
     */
    public function setSubtotalTax($subtotalTax);

    /**
     * @param $shipping
     * @param bool $withTax
     */
    public function setShipping($shipping, $withTax = true);

    /**
     * @param $taxRate
     */
    public function setShippingTaxRate($taxRate);

    /**
     * @return float
     */
    public function getShippingTax();

    /**
     * @param $shippingTax
     */
    public function setShippingTax($shippingTax);

    /**
     * @param $discount
     * @param bool $withTax
     */
    public function setDiscount($discount, $withTax = true);

    /**
     * @param $paymentFee
     * @param bool $withTax
     */
    public function setPaymentFee($paymentFee, $withTax = true);

    /**
     * @param $taxRate
     */
    public function setPaymentFeeTaxRate($taxRate);

    /**
     * @return Carbon
     */
    public function getOrderDate();

    /**
     * @param Carbon $orderDate
     */
    public function setOrderDate($orderDate);

    /**
     * @return string
     */
    public function getOrderNumber();

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber);

    /**
     * @return mixed
     */
    public function getCarrier();

    /**
     * @param $carrier
     *
     * @return mixed
     */
    public function setCarrier($carrier);

    /**
     * @return array
     */
    public function getPriceRules();

    /**
     * @return array
     */
    public function getPriceRuleItems();

    /**
     * @return bool
     */
    public function hasPriceRules();

    /**
     * @param $priceRule
     */
    public function addPriceRule($priceRule);

    /**
     * @param $priceRule
     */
    public function removePriceRule($priceRule);

    /**
     * @param $priceRule
     *
     * @return bool
     */
    public function hasPriceRule($priceRule);

    /**
     * @return PaymentInterface[]
     */
    public function getPayments();

    /**
     * @return float
     */
    public function getTotalPayed();

    /**
     * @return bool
     */
    public function getIsPayed();

    /**
     * @return float
     */
    public function getDiscountPercentage();

    /**
     * @param float $totalWeight
     */
    public function setTotalWeight($totalWeight);
}
