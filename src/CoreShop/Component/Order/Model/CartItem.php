<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class CartItem extends AbstractPimcoreModel implements CartItemInterface
{
    use AdjustableTrait;

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
    public function getTotal($withTax = true)
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotal($total, $withTax = true)
    {
        $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPrice($withTax = true)
    {
        return $withTax ? $this->getItemPriceGross() : $this->getItemPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPrice($itemPrice, $withTax = true)
    {
        $withTax ? $this->setItemPriceGross($itemPrice) : $this->setItemPriceNet($itemPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPrice($withTax = true)
    {
        return $withTax ? $this->getItemRetailPriceGross() : $this->getItemRetailPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemRetailPrice($itemRetailPrice, $withTax = true)
    {
        $withTax ? $this->setItemRetailPriceGross($itemRetailPrice) : $this->setItemRetailPriceNet($itemRetailPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPrice($withTax = true)
    {
        return $withTax ? $this->getItemDiscountPriceGross() : $this->getItemDiscountPriceNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountPrice($itemDiscountPrice, $withTax = true)
    {
        return $withTax ? $this->setItemDiscountPriceGross($itemDiscountPrice) : $this->setItemDiscountPriceNet($itemDiscountPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscount($withTax = true)
    {
        return $withTax ? $this->getItemDiscountGross() : $this->getItemDiscountNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscount($itemDiscount, $withTax = true)
    {
        return $withTax ? $this->setItemDiscountGross($itemDiscount) : $this->setItemDiscountNet($itemDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
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
     * @return CartInterface
     */
    public function getCart()
    {
        /**
         * @var CartInterface
         */
        $cart = $this->getParent();

        return $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalNet($totalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalGross($totalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPriceNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPriceNet($itemPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPriceGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPriceGross($itemPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPriceNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemRetailPriceNet($itemRetailPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPriceGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemRetailPriceGross($itemRetailPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPriceNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountPriceNet($itemDiscountPriceNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPriceGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountPriceGross($itemDiscountPriceGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountNet($itemDiscountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemDiscountGross($itemDiscountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemWholesalePrice()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemWholesalePrice($itemWholesalePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemTax($itemTax)
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
    protected function recalculateAfterAdjustmentChange()
    {
        $this->setTotal($this->getTotal(true) + $this->getAdjustmentsTotal(true), true);
        $this->setTotal($this->getTotal(false) + $this->getAdjustmentsTotal(false), false);
    }
}
