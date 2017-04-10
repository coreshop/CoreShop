<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Index\Model\FilterInterface;
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
     * @return FilterInterface
     */
    public function getFilter();

    /**
     * @param FilterInterface $filter
     */
    public function setFilter($filter);

    /**
     * @return CategoryInterface[]
     */
    public function getChildCategories();

    /**
     * @return CategoryInterface
     */
    public function hasChildCategories();

    /**
     * @return CategoryInterface[]
     */
    public function getHierarchy();
}
