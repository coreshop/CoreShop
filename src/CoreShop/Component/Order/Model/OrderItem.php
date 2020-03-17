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
    use BaseAdjustableTrait;

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
    public function getBaseItemPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseItemPriceGross() : $this->getBaseItemPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemPrice(int $itemPrice, bool $withTax = true)
    {
        return $withTax ? $this->setBaseItemPriceGross($itemPrice) : $this->setBaseItemPriceNet($itemPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemRetailPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseItemRetailPriceGross() : $this->getBaseItemRetailPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemRetailPrice(int $itemRetailPrice, bool $withTax = true)
    {
        return $withTax ? $this->setBaseItemRetailPriceGross($itemRetailPrice) : $this->setBaseItemRetailPriceNet(
            $itemRetailPrice
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseTotalGross() : $this->getBaseTotalNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTotal(int $total, bool $withTax = true)
    {
        return $withTax ? $this->setBaseTotalGross($total) : $this->setBaseTotalNet($total);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemDiscount(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseItemDiscountGross() : $this->getBaseItemDiscountNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemDiscount(int $itemDiscount, bool $withTax = true)
    {
        return $withTax ? $this->setBaseItemDiscountGross($itemDiscount) : $this->setBaseItemDiscountNet($itemDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemDiscountPrice(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseItemDiscountPriceGross() : $this->getBaseItemDiscountPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemDiscountPrice(int $itemDiscountPrice, bool $withTax = true)
    {
        return $withTax ? $this->setBaseItemDiscountPriceGross($itemDiscountPrice) : $this->setBaseItemDiscountPriceNet($itemDiscountPrice);
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
    public function getBaseItemPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemRetailPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemRetailPriceNet(int $itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemRetailPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemRetailPriceGross(int $itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemWholesalePrice(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemWholesalePrice(int $wholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemTax(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemTax(int $itemTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTaxes($taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }


    /**
     * {@inheritdoc}
     */
    public function getBaseItemDiscountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemDiscountNet(int $baseItemDiscountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemDiscountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemDiscountGross(int $baseItemDiscountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemDiscountPriceNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemDiscountPriceNet(int $baseItemDiscountPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseItemDiscountPriceGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseItemDiscountPriceGross(int $baseItemDiscountPriceGross)
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
    protected function recalculateBaseAfterAdjustmentChange()
    {
        $this->setBaseTotal($this->getBaseTotal(true) + $this->getBaseAdjustmentsTotal(null, true), true);
        $this->setBaseTotal($this->getBaseTotal(false) + $this->getBaseAdjustmentsTotal(null, false), false);
    }
}
