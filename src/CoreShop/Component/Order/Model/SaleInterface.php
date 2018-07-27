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
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\DataObject\Fieldcollection;

interface SaleInterface extends ProposalInterface, PimcoreModelInterface
{
    /**
     * @param PurchasableInterface $product
     *
     * @return CartItemInterface|null
     */
    public function getItemForProduct(PurchasableInterface $product);

    /**
     * @return CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @param CurrencyInterface $currency
     * @return mixed
     */
    public function setBaseCurrency($currency);

    /**
     * @param int $total
     * @param bool $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @param int $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @param int $subtotal
     * @param bool $withTax
     */
    public function setSubtotal($subtotal, $withTax = true);

    /**
     * @param int $subtotalTax
     */
    public function setSubtotalTax($subtotalTax);

    /**
     * @param int $shipping
     * @param bool $withTax
     */
    public function setShipping($shipping, $withTax = true);

    /**
     * @param int $taxRate
     */
    public function setShippingTaxRate($taxRate);

    /**
     * @return int
     */
    public function getShippingTax();

    /**
     * @param int $shippingTax
     */
    public function setShippingTax($shippingTax);

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
     * @return int
     */
    public function getDiscountPercentage();

    /**
     * @param float $weight
     */
    public function setWeight($weight);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getShipping($withTax = true);

    /**
     * @return int
     */
    public function getShippingTaxRate();


    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param int $total
     * @param bool $withTax
     */
    public function setBaseTotal($total, $withTax = true);

    /**
     * @return int
     */
    public function getBaseTotalTax();

    /**
     * @param int $totalTax
     */
    public function setBaseTotalTax($totalTax);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseSubtotal($withTax = true);

    /**
     * @param int $subtotal
     * @param bool $withTax
     */
    public function setBaseSubtotal($subtotal, $withTax = true);

    /**
     * @return int
     */
    public function getBaseSubtotalTax();

    /**
     * @param int $subtotalTax
     */
    public function setBaseSubtotalTax($subtotalTax);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseDiscount($withTax = true);

    /**
     * @param int $discount
     * @param bool $withTax
     */
    public function setBaseDiscount($discount, $withTax = true);

    /**
     * @return Fieldcollection
     */
    public function getBaseTaxes();

    /**
     * @param Fieldcollection $taxes
     */
    public function setBaseTaxes($taxes);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getBaseShipping($withTax = true);

    /**
     * @param int $shipping
     * @param bool $withTax
     */
    public function setBaseShipping($shipping, $withTax = true);

    /**
     * @return int
     */
    public function getBaseShippingTax();

    /**
     * @param int $shippingTax
     */
    public function setBaseShippingTax($shippingTax);

    /**
     * @return bool
     */
    public function getBackendCreated();

    /**
     * @param bool $backendCreated
     */
    public function setBackendCreated($backendCreated);
}
