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

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProposalItemInterface extends ResourceInterface, AdjustableInterface
{
    /**
     * @return PurchasableInterface
     */
    public function getProduct();

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @param PurchasableInterface $product
     */
    public function setProduct($product);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemPrice($withTax = true);

    /**
     * @param int  $itemPrice
     * @param bool $withTax
     */
    public function setItemPrice($itemPrice, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemRetailPrice($withTax = true);

    /**
     * @param int  $itemRetailPrice
     * @param bool $withTax
     */
    public function setItemRetailPrice($itemRetailPrice, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemDiscountPrice($withTax = true);

    /**
     * @param int  $itemDiscountPrice
     * @param bool $withTax
     */
    public function setItemDiscountPrice($itemDiscountPrice, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemDiscount($withTax = true);

    /**
     * @param int  $itemDiscount
     * @param bool $withTax
     */
    public function setItemDiscount($itemDiscount, $withTax = true);

    /**
     * @return int
     */
    public function getItemWholesalePrice();

    /**
     * @param int $itemWholesalePrice
     */
    public function setItemWholesalePrice($itemWholesalePrice);

    /**
     * @return int
     */
    public function getItemTax();

    /**
     * @param int $itemTax
     */
    public function setItemTax($itemTax);

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
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @return float
     */
    public function getItemWeight();

    /**
     * @return float
     */
    public function getTotalWeight();

    /**
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes);
}
