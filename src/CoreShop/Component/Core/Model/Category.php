<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Product\Model\Category as BaseCategory;
use CoreShop\Component\Resource\ImplementedByPimcoreException;

class Category extends BaseCategory implements CategoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMetaTitle($language = null)
    {
        return $this->getPimcoreMetaTitle($language) ?: $this->getName($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription($language = null)
    {
        return $this->getPimcoreMetaDescription($language) ?: $this->getDescription($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGTitle($language = null)
    {
        return $this->getMetaTitle($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGDescription($language = null)
    {
        return $this->getMetaDescription($language);
    }

    /**
     * {@inheritdoc}
     */
    public function getOGType()
    {
        return 'product.group';
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreMetaTitle($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreMetaTitle($pimcoreMetaTitle, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreMetaDescription($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreMetaDescription($pimcoreMetaDescription, $language = null)
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
    public function getStores()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($stores)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
