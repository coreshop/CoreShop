<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

interface OrderItemInterface extends
    PimcoreModelInterface,
    AdjustableInterface,
    ConvertedAdjustableInterface,
    StorageListItemInterface
{
    public function getOrder(): OrderInterface;

    public function getProduct(): ?PurchasableInterface;

    public function setProduct(?PurchasableInterface $product);

    public function getName(): ?string;

    public function setName(?string $name, $language = null);

    public function getTotal(bool $withTax = true): int;

    public function setTotal(int $total, bool $withTax = true);

    public function getIsGiftItem(): ?bool;

    public function setIsGiftItem(?bool $isGiftItem);

    public function getQuantity(): ?float;

    public function setQuantity(?float $quantity);

    public function getCustomItemPrice(): int;

    public function setCustomItemPrice(int $customItemPrice);

    public function getCustomItemDiscount(): ?float;

    public function setCustomItemDiscount(?float $customItemPrice);

    public function getItemPrice(bool $withTax = true): int;

    public function setItemPrice(int $itemPrice, bool $withTax = true);

    public function getItemRetailPrice(bool $withTax = true): int;

    public function setItemRetailPrice(int $itemRetailPrice, bool $withTax = true);

    public function getItemDiscountPrice(bool $withTax = true): int;

    public function setItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true);

    public function getItemDiscount(bool $withTax = true): int;

    public function setItemDiscount(int $itemDiscount, bool $withTax = true);

    public function getItemWholesalePrice(): int;

    public function setItemWholesalePrice(int $itemWholesalePrice);

    public function getItemTax(): int;

    public function setItemTax(int $itemTax);

    public function getTotalTax(): int;

    /**
     * @return Fieldcollection
     */
    public function getTaxes();

    /**
     * @param ?Fieldcollection $taxes
     */
    public function setTaxes(?Fieldcollection $taxes);

    /**
     * @return OrderItemUnitInterface[]
     */
    public function getUnits(): ?array;

    /**
     * @var OrderItemUnitInterface[] $units
     */
    public function setUnits(?array $units);

    public function hasUnit(OrderItemUnitInterface $itemUnit): bool;

    public function addUnit(OrderItemUnitInterface $itemUnit): void;

    public function removeUnit(OrderItemUnitInterface $itemUnit): void;

    public function setConvertedItemWholesalePrice(int $convertedItemWholesalePrice);

    public function getConvertedCustomItemPrice(): int;

    public function setConvertedCustomItemPrice(int $convertedCustomItemPrice);

    public function getConvertedItemPrice(bool $withTax = true): int;

    public function setConvertedItemPrice(int $itemPrice, bool $withTax = true);

    public function getConvertedItemRetailPrice(bool $withTax = true): int;

    public function setConvertedItemRetailPrice(int $itemRetailPrice, bool $withTax = true);

    public function getConvertedItemTax(): int;

    public function setConvertedItemTax(int $itemTax);

    /**
     * @return ?Fieldcollection
     */
    public function getConvertedTaxes();

    /**
     * @param ?Fieldcollection $taxes
     */
    public function setConvertedTaxes(?Fieldcollection $taxes);

    public function getConvertedTotal(bool $withTax = true): int;

    public function getConvertedTotalTax(): int;

    public function setConvertedTotal(int $total, bool $withTax = true);

    public function getConvertedItemDiscountPrice(bool $withTax = true): int;

    public function setConvertedItemDiscountPrice(int $convertedItemDiscountPrice, bool $withTax = true);

    public function getConvertedItemDiscount(bool $withTax = true): int;

    public function setConvertedItemDiscount(int $convertedItemDiscount, bool $withTax = true);
}
