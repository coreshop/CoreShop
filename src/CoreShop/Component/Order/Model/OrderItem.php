<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

abstract class OrderItem extends AbstractPimcoreModel implements OrderItemInterface
{
    use AdjustableTrait;
    use ConvertedAdjustableTrait;

    public function equals(StorageListItemInterface $storageListItem): bool
    {
        if ($this->getIsGiftItem()) {
            return false;
        }

        return $storageListItem->getProduct() instanceof PurchasableInterface &&
            $this->getProduct() instanceof PurchasableInterface &&
            $storageListItem->getProduct()->getId() === $this->getProduct()->getId();
    }

    public function getTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    public function setTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    public function getItemPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getItemPriceGross() : $this->getItemPriceNet();
    }

    public function setItemPrice(int $itemPrice, bool $withTax = true)
    {
        $withTax ? $this->setItemPriceGross($itemPrice) : $this->setItemPriceNet($itemPrice);
    }

    public function getItemRetailPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getItemRetailPriceGross() : $this->getItemRetailPriceNet();
    }

    public function setItemRetailPrice(int $itemRetailPrice, bool $withTax = true)
    {
        $withTax ? $this->setItemRetailPriceGross($itemRetailPrice) : $this->setItemRetailPriceNet($itemRetailPrice);
    }

    public function getItemDiscountPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getItemDiscountPriceGross() : $this->getItemDiscountPriceNet();
    }

    public function setItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true)
    {
        $withTax ? $this->setItemDiscountPriceGross($itemDiscountPrice) : $this->setItemDiscountPriceNet($itemDiscountPrice);
    }

    public function getItemDiscount(bool $withTax = true): int
    {
        return $withTax ? $this->getItemDiscountGross() : $this->getItemDiscountNet();
    }

    public function setItemDiscount(int $itemDiscount, bool $withTax = true)
    {
        $withTax ? $this->setItemDiscountGross($itemDiscount) : $this->setItemDiscountNet($itemDiscount);
    }

    public function getConvertedItemPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemPriceGross() : $this->getConvertedItemPriceNet();
    }

    public function setConvertedItemPrice(int $itemPrice, bool $withTax = true)
    {
        $withTax ? $this->setConvertedItemPriceGross($itemPrice) : $this->setConvertedItemPriceNet($itemPrice);
    }

    public function getConvertedItemRetailPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemRetailPriceGross() : $this->getConvertedItemRetailPriceNet();
    }

    public function setConvertedItemRetailPrice(int $itemRetailPrice, bool $withTax = true)
    {
        $withTax ? $this->setConvertedItemRetailPriceGross($itemRetailPrice) : $this->setConvertedItemRetailPriceNet(
            $itemRetailPrice
        );
    }

    public function getConvertedTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedTotalGross() : $this->getConvertedTotalNet();
    }

    public function setConvertedTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setConvertedTotalGross($total) : $this->setConvertedTotalNet($total);
    }

    public function getConvertedItemDiscount(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemDiscountGross() : $this->getConvertedItemDiscountNet();
    }

    public function setConvertedItemDiscount(int $itemDiscount, bool $withTax = true)
    {
        $withTax ? $this->setConvertedItemDiscountGross($itemDiscount) : $this->setConvertedItemDiscountNet($itemDiscount);
    }

    public function getConvertedItemDiscountPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemDiscountPriceGross() : $this->getConvertedItemDiscountPriceNet();
    }

    public function setConvertedItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true)
    {
        $withTax ? $this->setConvertedItemDiscountPriceGross($itemDiscountPrice) : $this->setConvertedItemDiscountPriceNet($itemDiscountPrice);
    }

    public function getTotalTax(): int
    {
        if (!$this->getTaxes() instanceof Fieldcollection) {
            return 0;
        }

        $totalTax = 0;

        foreach ($this->getTaxes()->getItems() as $taxItem) {
            if (!$taxItem instanceof TaxItemInterface) {
                continue;
            }

            $totalTax += $taxItem->getAmount();
        }

        return $totalTax;
    }

    public function getConvertedTotalTax(): int
    {
        if (!$this->getConvertedTaxes() instanceof Fieldcollection) {
            return 0;
        }

        $totalTax = 0;

        foreach ($this->getConvertedTaxes()->getItems() as $taxItem) {
            if (!$taxItem instanceof TaxItemInterface) {
                continue;
            }

            $totalTax += $taxItem->getAmount();
        }

        return $totalTax;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof OrderInterface) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent !== null);

        throw new \Exception('Order Item does not have a valid Order');
    }

    public function getTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTotalNet(int $totalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTotalGross(int $totalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getCustomItemPrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setCustomItemPrice(int $customItemPrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getCustomItemDiscount(): ?float
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setCustomItemDiscount(?float $customItemPrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemRetailPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemRetailPriceNet(int $itemRetailPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemRetailPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemRetailPriceGross(int $itemRetailPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemDiscountPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemDiscountPriceNet(int $itemDiscountPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemDiscountPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemDiscountPriceGross(int $itemDiscountPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemDiscountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemDiscountNet(int $itemDiscountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemDiscountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemDiscountGross(int $itemDiscountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemWholesalePrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemWholesalePrice(int $itemWholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getItemTax(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setItemTax(int $itemTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getProduct(): ?PurchasableInterface
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setProduct(?PurchasableInterface $product)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getQuantity(): ?float
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setQuantity(?float $quantity)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getIsGiftItem(): ?bool
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setIsGiftItem(?bool $isGiftItem)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTaxes(?Fieldcollection $taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedCustomItemPrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedCustomItemPrice(int $convertedCustomItemPrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemRetailPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemRetailPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemRetailPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemRetailPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemWholesalePrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemWholesalePrice(int $wholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemTax(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemTax(int $itemTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTaxes(?Fieldcollection $taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }


    public function getConvertedItemDiscountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemDiscountNet(int $convertedItemDiscountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemDiscountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemDiscountGross(int $convertedItemDiscountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemDiscountPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemDiscountPriceNet(int $convertedItemDiscountPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedItemDiscountPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedItemDiscountPriceGross(int $convertedItemDiscountPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    protected function recalculateAfterAdjustmentChange(): void
    {
        $this->setTotal($this->getTotal(true) + $this->getAdjustmentsTotal(null, true), true);
        $this->setTotal($this->getTotal(false) + $this->getAdjustmentsTotal(null, false), false);
    }

    protected function recalculateConvertedAfterAdjustmentChange(): void
    {
        $this->setConvertedTotal($this->getConvertedTotal(true) + $this->getConvertedAdjustmentsTotal(null, true), true);
        $this->setConvertedTotal($this->getConvertedTotal(false) + $this->getConvertedAdjustmentsTotal(null, false), false);
    }
}
