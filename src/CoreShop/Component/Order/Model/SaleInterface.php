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
use Pimcore\Model\Object\Fieldcollection;

interface SaleInterface extends ProposalInterface, PimcoreModelInterface
{
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
    public function getSaleLanguage();

    /**
     * @param $saleLanguage
     */
    public function setSaleLanguage($saleLanguage);

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
     * @return Fieldcollection
     */
    public function getTaxes();

    /**
     * @param Fieldcollection $taxes
     */
    public function setTaxes($taxes);

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
     * @return Carbon
     */
    public function getSaleDate();

    /**
     * @param Carbon $saleDate
     */
    public function setSaleDate($saleDate);

    /**
     * @return string
     */
    public function getSaleNumber();

    /**
     * @param string $saleNumber
     */
    public function setSaleNumber($saleNumber);

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
     * @return float
     */
    public function getDiscountPercentage();

    /**
     * @param float $weight
     */
    public function setWeight($weight);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getShipping($withTax = true);

    /**
     * @return float
     */
    public function getShippingTaxRate();
}
