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

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

abstract class OrderItem extends AbstractPimcoreModel implements OrderItemInterface
{
    use AdjustableTrait;
    use ConvertedAdjustableTrait;

    /**
     * {@inheritdoc}
     */
    public function equals(StorageListItemInterface $storageListItem)
    {
        if ($this->getIsGiftItem()) {
            return false;
        }

        return $storageListItem->getProduct() instanceof PurchasableInterface &&
            $this->getProduct() instanceof PurchasableInterface &&
            $storageListItem->getProduct()->getId() === $this->getProduct()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotal(int $total, bool $withTax = true)
    {
        return $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getItemPriceGross() : $this->getItemPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPrice(int $itemPrice, bool $withTax = true)
    {
        return $withTax ? $this->setItemPriceGross($itemPrice) : $this->setItemPriceNet($itemPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getItemRetailPriceGross() : $this->getItemRetailPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemRetailPrice(int $itemRetailPrice, bool $withTax = true)
    {
        return $withTax ? $this->setItemRetailPriceGross($itemRetailPrice) : $this->setItemRetailPriceNet($itemRetailPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getItemDiscountPriceGross() : $this->getItemDiscountPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true)
    {
        return $withTax ? $this->setItemDiscountPriceGross($itemDiscountPrice) : $this->setItemDiscountPriceNet($itemDiscountPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscount(bool $withTax = true): int
    {
        return $withTax ? $this->getItemDiscountGross() : $this->getItemDiscountNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscount(int $itemDiscount, bool $withTax = true)
    {
        return $withTax ? $this->setItemDiscountGross($itemDiscount) : $this->setItemDiscountNet($itemDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemPriceGross() : $this->getConvertedItemPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemPrice(int $itemPrice, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedItemPriceGross($itemPrice) : $this->setConvertedItemPriceNet($itemPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemRetailPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemRetailPriceGross() : $this->getConvertedItemRetailPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemRetailPrice(int $itemRetailPrice, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedItemRetailPriceGross($itemRetailPrice) : $this->setConvertedItemRetailPriceNet(
            $itemRetailPrice
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedTotalGross() : $this->getConvertedTotalNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedTotal(int $total, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedTotalGross($total) : $this->setConvertedTotalNet($total);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemDiscount(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemDiscountGross() : $this->getConvertedItemDiscountNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemDiscount(int $itemDiscount, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedItemDiscountGross($itemDiscount) : $this->setConvertedItemDiscountNet($itemDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemDiscountPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedItemDiscountPriceGross() : $this->getConvertedItemDiscountPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedItemDiscountPriceGross($itemDiscountPrice) : $this->setConvertedItemDiscountPriceNet($itemDiscountPrice);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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
    public function getOrder()
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

    /**
     * {@inheritdoc}
     */
    public function getTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalNet(int $totalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalGross(int $totalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomItemPrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomItemPrice(int $customItemPrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomItemDiscount()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomItemDiscount($customItemPrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemRetailPriceNet(int $itemRetailPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemRetailPriceGross(int $itemRetailPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountPriceNet(int $itemDiscountPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountPriceGross(int $itemDiscountPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountNet(int $itemDiscountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountGross(int $itemDiscountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemWholesalePrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemWholesalePrice(int $itemWholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTax(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemTax(int $itemTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsGiftItem()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsGiftItem($isGiftItem)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxes($taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedCustomItemPrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedCustomItemPrice(int $convertedCustomItemPrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemRetailPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemRetailPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemRetailPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemRetailPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemWholesalePrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemWholesalePrice(int $wholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemTax(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemTax(int $itemTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedTaxes($taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }


    /**
     * {@inheritdoc}
     */
    public function getConvertedItemDiscountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemDiscountNet(int $convertedItemDiscountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemDiscountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemDiscountGross(int $convertedItemDiscountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemDiscountPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemDiscountPriceNet(int $convertedItemDiscountPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedItemDiscountPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedItemDiscountPriceGross(int $convertedItemDiscountPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    protected function recalculateAfterAdjustmentChange()
    {
        $this->setTotal($this->getTotal(true) + $this->getAdjustmentsTotal(null, true), true);
        $this->setTotal($this->getTotal(false) + $this->getAdjustmentsTotal(null, false), false);
    }

    /**
     * {@inheritdoc}
     */
    protected function recalculateConvertedAfterAdjustmentChange()
    {
        $this->setConvertedTotal($this->getConvertedTotal(true) + $this->getConvertedAdjustmentsTotal(null, true), true);
        $this->setConvertedTotal($this->getConvertedTotal(false) + $this->getConvertedAdjustmentsTotal(null, false), false);
    }
}
