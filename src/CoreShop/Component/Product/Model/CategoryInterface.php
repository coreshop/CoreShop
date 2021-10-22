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

declare(strict_types=1);

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CategoryInterface extends PimcoreModelInterface, SluggableInterface
{
    public function getName(?string $language = null): ?string;

    public function setName(?string $name, ?string $language = null);

    public function getDescription(?string $language = null): ?string;

    public function setDescription(?string $description, ?string $language = null);

    public function getParentCategory(): ?self;

    public function setParentCategory(?self $parentCategory);

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
