<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Product\Model\Product as BaseProduct;
use CoreShop\Component\Resource\ImplementedByPimcoreException;

class Product extends BaseProduct implements ProductInterface
{
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
    public function getEnabled()
    {
        return $this->getActive();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexable()
    {
        return true;
    }
}