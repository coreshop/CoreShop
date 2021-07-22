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

declare(strict_types=1);

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CategoryInterface extends PimcoreModelInterface, SluggableInterface
{
    public function getName($language = null): ?string;

    public function setName(?string $name, $language = null);

    public function getDescription($language = null): ?string;

    public function setDescription(?string$description, $language = null);

    public function getParentCategory(): ?CategoryInterface;

    public function setParentCategory(?CategoryInterface $parentCategory);

    /**
     * @return CategoryInterface[]
     */
    public function getChildCategories(): array;

    public function hasChildCategories(): bool;

    /**
     * @return CategoryInterface[]
     */
    public function getHierarchy(): array;
}
