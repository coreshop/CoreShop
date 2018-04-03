<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Product\Model\Product as BaseProduct;
use CoreShop\Component\Resource\ImplementedByPimcoreException;

class Product extends BaseProduct implements ProductInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return 0 < $this->getOnHand();
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold($onHold)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsTracked()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsTracked($tracked)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRule()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRule($taxRule)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($stores)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorePrice(\CoreShop\Component\Store\Model\StoreInterface $store = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStorePrice(int $price, \CoreShop\Component\Store\Model\StoreInterface $store = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDigitalProduct()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDigitalProduct($digitalProduct)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->getActive() && $this->getPublished();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return true;
    }
}