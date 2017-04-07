<?php

namespace CoreShop\Component\Product\Pimcore\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
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

    /**
     * {@inheritdoc}
     *
     * TODO: implement me
     */
    public function getChildCategories()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * TODO: implement me
     */
    public function hasChildCategories()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * TODO: implement me
     */
    public function addChildCategory($category)
    {

    }

    /**
     * {@inheritdoc}
     *
     * TODO: implement me
     */
    public function removeChildCategory($category)
    {

    }

    /**
     * {@inheritdoc}
     *
     * TODO: implement me
     */
    public function hasChildCategory($category)
    {
        return false;
    }
}
