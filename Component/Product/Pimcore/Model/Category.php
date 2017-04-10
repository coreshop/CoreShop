<?php

namespace CoreShop\Component\Product\Pimcore\Model;

use CoreShop\Component\Index\Model\FilterInterface;
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
     */
    public function getFilter()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter($filter)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildCategories()
    {
        return $this->getChildren();
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildCategories()
    {
        return count($this->getChildren());
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchy()
    {
        $hierarchy = [];

        $category = $this;

        do {
            $hierarchy[] = $category;

            $category = $category->getParent();
        } while ($category instanceof self);

        return array_reverse($hierarchy);
    }
}
