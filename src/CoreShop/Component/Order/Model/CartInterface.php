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


use CoreShop\Component\Order\Checkout\CheckoutAwareInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;


interface CartInterface extends ProposalInterface, PimcoreModelInterface, CheckoutAwareInterface, StorageListInterface
{
    /**
     * @param $order
     */
    public function setOrder($order);

    /**
     * @return mixed
     */
    public function getOrder();

    /**
     * @return array
     */
    public function getPriceRuleItems();

    /**
     * @param array $priceRuleItems
     */
    public function setPriceRuleItems($priceRuleItems);

    /**
     * @return array
     */
    public function getPriceRules();

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
     * @param bool $withTax
     *
     * @return int
     */
    public function getPaymentFee($withTax = true);

    /**
     * @return mixed
     */
    public function getPaymentProvider();

    /**
     * @param $paymentProvider
     *
     * @return mixed
     */
    public function setPaymentProvider($paymentProvider);

    /**
     * @return int
     */
    public function getPaymentFeeTaxRate();

    /**
     * @return bool
     */
    public function hasShippableItems();
}
