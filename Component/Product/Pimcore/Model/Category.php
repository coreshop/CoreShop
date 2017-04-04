<?php

namespace CoreShop\Component\Product\Pimcore\Model;

use CoreShop\Component\Core\ImplementedByPimcoreException;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class Category extends AbstractPimcoreModel implements CategoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

}
