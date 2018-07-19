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
    public function getMetaTitle($language = null)
    {
        return $this->getPimcoreMetaTitle($language) ?: $this->getName($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription($language = null)
    {
        return $this->getPimcoreMetaDescription($language) ?: $this->getShortDescription($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGTitle($language = null)
    {
        return $this->getMetaTitle($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGDescription($language = null)
    {
        return $this->getMetaDescription($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGType()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreMetaTitle($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreMetaTitle($pimcoreMetaTitle, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreMetaDescription($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreMetaDescription($pimcoreMetaDescription, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
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
    public function setStorePrice($price, \CoreShop\Component\Store\Model\StoreInterface $store = null)
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
    public function getIndexableEnabled()
    {
        return $this->getActive() && $this->getPublished();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return $this->getIndexableEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexableName($language)
    {
        return $this->getName($language);
    }
}