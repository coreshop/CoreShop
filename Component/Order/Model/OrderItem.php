<?php

namespace CoreShop\Component\Order\Pimcore\Model;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class OrderItem extends AbstractPimcoreModel implements OrderItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        $parent = $this->getParent();

        do {
            if (is_subclass_of($parent, OrderInterface::class)) {
                return $parent;
            }
            $parent = $parent->getParent();
        } while ($parent != null);

        return false;
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
        return $withTax ? $this->setItemPriceGross($itemPrice) : $this->setItemPriceNet($itemPrice);
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
        return $withTax ? $this->setItemRetailPriceGross($itemRetailPrice) : $this->setItemRetailPriceNet($itemRetailPrice);
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
        return $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
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
    public function setItemRetailPriceNet($itemPriceNet)
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
    public function setItemRetailPriceGross($itemPriceGross)
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
    public function setItemWholesalePrice($wholesalePrice)
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
    public function getTaxes($applyDiscountToTaxValues = true)
    {
        throw new \Exception("implement me");
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxes($taxes)
    {
        throw new \Exception("implement me");
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
    public function setTotalNet($total)
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
    public function setTotalGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalTax($totalTax)
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
}