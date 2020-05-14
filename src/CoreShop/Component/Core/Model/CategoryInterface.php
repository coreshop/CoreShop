<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Product\Model\CategoryInterface as BaseCategoryInterface;
use CoreShop\Component\SEO\Model\PimcoreSEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;

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
