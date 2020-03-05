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

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

interface OrderItemInterface extends PimcoreModelInterface, AdjustableInterface, BaseAdjustableInterface, StorageListItemInterface
{
    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @return PurchasableInterface
     */
    public function getProduct();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string      $name
     * @param string|null $language
     */
    public function setName($name, $language = null);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal($withTax = true);

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal($total, $withTax = true);

    /**
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @param bool $isGiftItem
     */
    public function setIsGiftItem($isGiftItem);

    /**
     * @param PurchasableInterface $product
     */
    public function setProduct($product);

    /**
     * @return float
     */
    public function getQuantity();

    /**
     * @param float $quantity
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
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes);

    /**
     * @param int $baseItemWholesalePrice
     */
    public function setBaseItemWholesalePrice($baseItemWholesalePrice);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemPrice($withTax = true);

    /**
     * @param int  $itemPrice
     * @param bool $withTax
     */
    public function setBaseItemPrice($itemPrice, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemRetailPrice($withTax = true);

    /**
     * @param int  $itemRetailPrice
     * @param bool $withTax
     */
    public function setBaseItemRetailPrice($itemRetailPrice, $withTax = true);

    /**
     * @return int
     */
    public function getBaseItemTax();

    /**
     * @param int $itemTax
     */
    public function setBaseItemTax($itemTax);

    /**
     * @return mixed
     */
    public function getBaseTaxes();

    /**
     * @param mixed $taxes
     */
    public function setBaseTaxes($taxes);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setBaseTotal($total, $withTax = true);
}
