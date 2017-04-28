<?php

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

class CustomerGroup extends AbstractPimcoreModel implements CustomerGroupInterface, PimcoreModelInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShops()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShops($shops)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
