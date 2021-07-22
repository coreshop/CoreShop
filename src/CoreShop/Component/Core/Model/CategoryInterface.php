<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Product\Model\CategoryInterface as BaseCategoryInterface;
use CoreShop\Component\SEO\Model\PimcoreSEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;
use CoreShop\Component\Store\Model\StoresAwareInterface;

interface CategoryInterface extends
    BaseCategoryInterface,
    PimcoreSEOAwareInterface,
    SEOOpenGraphAwareInterface,
    PimcoreStoresAwareInterface
{
    /**
     * @return FilterInterface
     */
    public function getFilter();

    /**
     * @param FilterInterface $filter
     */
    public function setFilter($filter);
}
