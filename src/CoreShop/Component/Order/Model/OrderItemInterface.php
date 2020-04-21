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

interface OrderItemInterface extends
    PimcoreModelInterface,
    AdjustableInterface,
    ConvertedAdjustableInterface,
    StorageListItemInterface
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
    public function getTotal(bool $withTax = true): int;

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal(int $total, bool $withTax = true);

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
     * @return int
     */
    public function getCustomItemPrice(): int;

    /**
     * @param int $customItemPrice
     */
    public function setCustomItemPrice(int $customItemPrice);

    /**
     * @return int
     */
    public function getCustomItemDiscount();

    /**
     * @param int $customItemPrice
     */
    public function setCustomItemDiscount($customItemPrice);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemPrice(bool $withTax = true): int;

    /**
     * @param int  $itemPrice
     * @param bool $withTax
     */
    public function setItemPrice(int $itemPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemRetailPrice(bool $withTax = true): int;

    /**
     * @param int  $itemRetailPrice
     * @param bool $withTax
     */
    public function setItemRetailPrice(int $itemRetailPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemDiscountPrice(bool $withTax = true): int;

    /**
     * @param int  $itemDiscountPrice
     * @param bool $withTax
     */
    public function setItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getItemDiscount(bool $withTax = true): int;

    /**
     * @param int  $itemDiscount
     * @param bool $withTax
     */
    public function setItemDiscount(int $itemDiscount, bool $withTax = true);

    /**
     * @return int
     */
    public function getItemWholesalePrice(): int;

    /**
     * @param int $itemWholesalePrice
     */
    public function setItemWholesalePrice(int $itemWholesalePrice);

    /**
     * @return int
     */
    public function getItemTax(): int;

    /**
     * @param int $itemTax
     */
    public function setItemTax(int $itemTax);

    /**
     * @return int
     */
    public function getTotalTax(): int;

    /**
     * @return mixed
     */
    public function getTaxes();

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes);

    /**
     * @param int $convertedItemWholesalePrice
     */
    public function setConvertedItemWholesalePrice(int $convertedItemWholesalePrice);

    /**
     * @return int
     */
    public function getConvertedCustomItemPrice(): int;

    /**
     * @param int $convertedCustomItemPrice
     */
    public function setConvertedCustomItemPrice(int $convertedCustomItemPrice);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedItemPrice(bool $withTax = true): int;

    /**
     * @param int  $itemPrice
     * @param bool $withTax
     */
    public function setConvertedItemPrice(int $itemPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedItemRetailPrice(bool $withTax = true): int;

    /**
     * @param int  $itemRetailPrice
     * @param bool $withTax
     */
    public function setConvertedItemRetailPrice(int $itemRetailPrice, bool $withTax = true);

    /**
     * @return int
     */
    public function getConvertedItemTax(): int;

    /**
     * @param int $itemTax
     */
    public function setConvertedItemTax(int $itemTax);

    /**
     * @return mixed
     */
    public function getConvertedTaxes();

    /**
     * @param mixed $taxes
     */
    public function setConvertedTaxes($taxes);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedTotal(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getConvertedTotalTax(): int;

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setConvertedTotal(int $total, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedItemDiscountPrice(bool $withTax = true): int;

    /**
     * @param int  $convertedItemDiscountPrice
     * @param bool $withTax
     */
    public function setConvertedItemDiscountPrice(int $convertedItemDiscountPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedItemDiscount(bool $withTax = true): int;

    /**
     * @param int  $convertedItemDiscount
     * @param bool $withTax
     */
    public function setConvertedItemDiscount(int $convertedItemDiscount, bool $withTax = true);
}
