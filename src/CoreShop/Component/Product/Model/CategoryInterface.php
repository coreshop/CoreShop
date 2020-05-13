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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CategoryInterface extends PimcoreModelInterface
{
    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language = null);

    /**
     * @param string $name
     * @param string $language
     *
     * @return string
     */
    public function setName($name, $language = null);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getDescription($language = null);

    /**
     * @param string $description
     * @param string $language
     *
     * @return string
     */
    public function setDescription($description, $language = null);

    /**
     * @return CategoryInterface
     */
    public function getParentCategory();

    /**
     * @param CategoryInterface $parentCategory
     */
    public function setParentCategory($parentCategory);

    /**
     * @return CategoryInterface[]
     */
    public function getChildCategories();

    /**
     * @return bool
     */
    public function hasChildCategories();

    /**
     * @return CategoryInterface[]
     */
    public function getHierarchy();
}
