<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Product\Filter;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Object;
use Pimcore\Model\Asset\Image;

/**
 * Class Category
 * @package CoreShop\Model
 * 
 * @method static Object\Listing\Concrete getByParentCategory ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByLocalizedfields ($field, $value, $locale = null, $limit = 0)
 * @method static Object\Listing\Concrete getByCategoryImage ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShops ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByFilterDefinition ($value, $limit = 0)
 */
class Category extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopCategory';

    /**
     * @var string
     */
    public static $staticRoute = "coreshop_list";

    /**
     * Get all Categories.
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::getList();

        return $list->load();
    }

    /**
     * Get first level of categories.
     *
     * @return array
     */
    public static function getFirstLevel()
    {
        $list = self::getList();
        $list->setCondition("parentCategory__id is null AND shops LIKE '%,".Shop::getShop()->getId().",%'");

        return $list->load();
    }

    /**
     * Returns all Child Categories from $category.
     *
     * @param Category $category
     *
     * @return array
     */
    public static function getAllChildCategories(Category $category)
    {
        $allChildren = array($category->getId());

        $loopChilds = function (Category $child) use (&$loopChilds, &$allChildren) {
            $childs = $child->getChildCategories();

            foreach ($childs as $child) {
                $allChildren[] = $child->getId();

                $loopChilds($child);
            }
        };

        $loopChilds($category);

        return $allChildren;
    }

    /**
     * Get Products from the Category.
     *
     * @param bool $includeChildCategories
     *
     * @return array
     */
    public function getProducts($includeChildCategories = false)
    {
        $list = Product::getList();

        if (!$includeChildCategories) {
            $list->setCondition("enabled = 1 AND categories LIKE '%,".$this->getId().",%'");
        } else {
            $categories = $this->getCatChilds();
            $categoriesWhere = array();

            foreach ($categories as $cat) {
                $categoriesWhere[] = "categories LIKE '%,".$cat.",%'";
            }

            $list->setCondition('enabled = 1 AND ('.implode(' OR ', $categoriesWhere).')');
        }

        return $list->load();
    }

    /**
     * Get Products from the Category with Paging.
     *
     * @param int   $page
     * @param int   $itemsPerPage
     * @param array $sort
     * @param bool  $includeChildCategories
     *
     * @return \Zend_Paginator
     *
     * @throws \Zend_Paginator_Exception
     */
    public function getProductsPaging($page = 0, $itemsPerPage = 10, $sort = array('name' => 'name', 'direction' => 'asc'), $includeChildCategories = false)
    {
        $list = Product::getList();

        $condition = "enabled = 1";

        if (!$includeChildCategories) {
            $condition .= " AND categories LIKE '%,".$this->getId().",%'";
        } else {
            $categories = $this->getCatChilds();
            $categoriesWhere = array();

            foreach ($categories as $cat) {
                $categoriesWhere[] = "categories LIKE '%,".$cat.",%'";
            }

            $condition .= ' AND ('.implode(' OR ', $categoriesWhere).')';
        }

        $condition .= " AND shops LIKE '%,".Shop::getShop()->getId().",%'";

        $list->setCondition($condition);
        $list->setOrderKey($sort['name']);
        $list->setOrder($sort['direction']);

        $paginator = \Zend_Paginator::factory($list);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        return $paginator;
    }

    /**
     * Checks if category is child of hierachy.
     *
     * @param Category $category
     * @param int      $level    to check hierachy (0 = topMost)
     *
     * @return bool
     */
    public function inCategory(Category $category, $level = 0)
    {
        $mostTop = $this->getHierarchy();
        $mostTop = $mostTop[$level];

        $childs = self::getAllChildCategories($mostTop);

        return in_array($category->getId(), $childs);
    }

    /**
     * Get Level of Category.
     *
     * @return int
     */
    public function getLevel()
    {
        return count($this->getHierarchy());
    }

    /**
     * Returns all Children from this Category.
     *
     * @return array
     */
    public function getCatChilds()
    {
        return self::getAllChildCategories($this);
    }

    /**
     * Get default image.
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getDefaultImage()
    {
        $defaultImage = Configuration::get('SYSTEM.CATEGORY.DEFAULTIMAGE');

        if ($defaultImage) {
            $image = Image::getByPath($defaultImage);

            if ($image instanceof Image) {
                return $image;
            }
        }

        return false;
    }

    /**
     * Get image.
     *
     * @return bool|\Pimcore\Model\Asset
     */
    public function getImage()
    {
        if ($this->getCategoryImage() instanceof Image) {
            return $this->getCategoryImage();
        }

        return $this->getDefaultImage();
    }

    /**
     * Get Category hierarchy.
     *
     * @return Category[]
     */
    public function getHierarchy()
    {
        $hierarchy = array();

        $category = $this;

        do {
            $hierarchy[] = $category;

            $category = $category->getParentCategory();
        } while ($category instanceof self);

        return array_reverse($hierarchy);
    }

    /**
     * Get all child Categories.
     *
     * @return array
     */
    public function getChildCategories()
    {
        $list = Category::getList();
        $list->setCondition("parentCategory__id = ? AND shops LIKE '%,".Shop::getShop()->getId().",%'", array($this->getId()));

        return $list->load();
    }

    /**
     * get url for category -> returns false if the category is not available for the shop
     *
     * @param $language
     * @param bool $reset
     * @param Shop|null $shop
     *
     * @return bool|string
     */
    public function getCategoryUrl($language, $reset = false, Shop $shop = null) {
        return $this->getUrl($language, ["category" => $this->getId(), "name" => File::getValidFilename($this->getName())], static::$staticRoute, $reset, $shop);
    }

    /**
     * @return Category
     *
     * @throws ObjectUnsupportedException
     */
    public function getParentCategory()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Category $parentCategory
     *
     * @throws ObjectUnsupportedException
     */
    public function setParentCategory($parentCategory)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Asset
     *
     * @throws ObjectUnsupportedException
     */
    public function getCategoryImage()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Asset $categoryImage
     *
     * @throws ObjectUnsupportedException
     */
    public function setCategoryImage($categoryImage)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return int[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getShops()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int[] $shops
     *
     * @throws ObjectUnsupportedException
     */
    public function setShops($shops)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Filter|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getFilterDefinition()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Filter $filterDefinition
     *
     * @throws ObjectUnsupportedException
     */
    public function setFilterDefinition($filterDefinition)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
