<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CategoryInterface extends PimcoreModelInterface
{
    /**
     * @param null $language
     *
     * @return mixed
     */
    public function getName($language = null);

    /**
     * @param $name
     * @param null $language
     *
     * @return mixed
     */
    public function setName($name, $language = null);

    /**
     * @return CategoryInterface[]
     */
    public function getChildCategories();

    /**
     * @return CategoryInterface
     */
    public function hasChildCategories();

    /**
     * @param CategoryInterface $category
     */
    public function addChildCategory($category);

    /**
     * @param $category
     */
    public function removeChildCategory($category);

    /**
     * @param $category
     *
     * @return bool
     */
    public function hasChildCategory($category);
}
