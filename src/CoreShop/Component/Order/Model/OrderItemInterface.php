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
    BaseAdjustableInterface,
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
     * @param int $baseItemWholesalePrice
     */
    public function setBaseItemWholesalePrice(int $baseItemWholesalePrice);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemPrice(bool $withTax = true): int;

    /**
     * @param int  $itemPrice
     * @param bool $withTax
     */
    public function setBaseItemPrice(int $itemPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemRetailPrice(bool $withTax = true): int;

    /**
     * @param int  $itemRetailPrice
     * @param bool $withTax
     */
    public function setBaseItemRetailPrice(int $itemRetailPrice, bool $withTax = true);

    /**
     * @return int
     */
    public function getBaseItemTax(): int;

    /**
     * @param int $itemTax
     */
    public function setBaseItemTax(int $itemTax);

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
    public function getBaseTotal(bool $withTax = true): int;

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setBaseTotal(int $total, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemDiscountPrice(bool $withTax = true): int;

    /**
     * @param int  $baseItemDiscountPrice
     * @param bool $withTax
     */
    public function setBaseItemDiscountPrice(int $baseItemDiscountPrice, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemDiscount(bool $withTax = true): int;

    /**
     * @param int  $baseItemDiscount
     * @param bool $withTax
     */
    public function setBaseItemDiscount(int $baseItemDiscount, bool $withTax = true);
}
