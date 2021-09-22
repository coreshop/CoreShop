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

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

abstract class Category extends AbstractPimcoreModel implements CategoryInterface
{
    public function getChildCategories(): array
    {
        /**
         * @var CategoryInterface[] $childs
         */
        $childs = $this->getChildren();

        return $childs;
    }

    public function hasChildCategories(): bool
    {
        return count($this->getChildren()) > 0;
    }

    public function getNameForSlug(string $language = null): ?string
    {
        return $this->getName($language);
    }

    public function getHierarchy(): array
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
