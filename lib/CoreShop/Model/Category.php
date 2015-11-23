<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model;

use Pimcore\Model\Object;
use Pimcore\Model\Asset\Image;

use CoreShop\Config;

class Category extends Base {

    /**
     * Get all Categories
     *
     * @return array
     */
    public static function getAll()
    {
        $list = new Object\CoreShopCategory\Listing();
        
        return $list->getObjects();
    }

    /**
     * Get first level of categories
     *
     * @return array
     */
    public static function getFirstLevel()
    {
        $list = new Object\CoreShopCategory\Listing();
        $list->setCondition("parentCategory__id is null");

        return $list->getObjects();
    }

    /**
     * Get Products from the Category
     *
     * @return array
     */
    public function getProducts()
    {
        $list = new Object\CoreShopProduct\Listing();
        $list->setCondition("enabled = 1 AND categories LIKE '%,".$this->getId().",%'");

        return $list->getObjects();
    }

    /**
     * Get Products from the Category with Paging
     *
     * @param int $page
     * @param int $itemsPerPage
     * @param array $sort
     * @return \Zend_Paginator
     * @throws \Zend_Paginator_Exception
     */
    public function getProductsPaging($page = 0, $itemsPerPage = 10, $sort = array("name" => "name", "direction" => "asc"))
    {
        $list = new Object\CoreShopProduct\Listing();
        $list->setCondition("enabled = 1 AND categories LIKE '%,".$this->getId().",%'");

        $list->setOrderKey($sort['name']);
        $list->setOrder($sort['direction']);

        $paginator = \Zend_Paginator::factory($list);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        return $paginator;
    }

    /**
     * Get default image
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getDefaultImage()
    {
        $config = Config::getConfig();
        $config = $config->toArray();
        $image = Image::getByPath($config['category']['default-image']);

        if($image instanceof Image)
            return $image;

        return false;
    }

    /**
     * Get image
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getImage()
    {
        if($this->getCategoryImage() instanceof Image)
        {
            return $this->getCategoryImage();
        }

        return $this->getDefaultImage();
    }

    /**
     * Get Category hierarchy
     *
     * @return array
     */
    public function getHierarchy()
    {
        $hierarchy = array();

        $category = $this;

        do {
            $hierarchy[] = $category;

            $category = $category->getParentCategory();
        }
        while($category instanceof Object\CoreShopCategory);

        return array_reverse($hierarchy);
    }

    /**
     * Get all child Categories
     *
     * @return array
     */
    public function getChildCategories()
    {
        $list = new Object\CoreShopCategory\Listing();
        $list->setCondition("parentCategory__id = ?", array($this->getId()));

        return $list->getObjects();
    }
}