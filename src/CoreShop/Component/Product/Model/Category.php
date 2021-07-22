<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
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
    public function getDescription($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentCategory()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentCategory($parentCategory)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildCategories()
    {
        /**
         * @var CategoryInterface[] $childs
         */
        $childs = $this->getChildren();

        return $childs;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildCategories()
    {
        return count($this->getChildren()) > 0;
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

        /**
         * @var CategoryInterface[] $hierarchy
         */
        $hierarchy = array_reverse($hierarchy);

        return $hierarchy;
    }
}
