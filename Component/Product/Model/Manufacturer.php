<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class Manufacturer extends AbstractPimcoreModel implements ManufacturerInterface
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
