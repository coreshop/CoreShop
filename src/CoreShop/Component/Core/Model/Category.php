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

use CoreShop\Component\Product\Model\Category as BaseCategory;

abstract class Category extends BaseCategory implements CategoryInterface
{
    public function getMetaTitle(?string $language = null): ?string
    {
        return $this->getPimcoreMetaTitle($language) ?: $this->getName($language);
    }

    public function getMetaDescription(?string $language = null): ?string
    {
        if (null !== $this->getPimcoreMetaDescription($language)) {
            return $this->getPimcoreMetaDescription($language);
        }

        if (null !== $this->getDescription($language)) {
            return strip_tags($this->getDescription($language));
        }

        return null;
    }

    public function getOGTitle(?string $language = null): ?string
    {
        return $this->getMetaTitle($language);
    }

    public function getOGDescription(?string $language = null): ?string
    {
        return $this->getMetaDescription($language);
    }

    public function getOGType(): ?string
    {
        return 'product.group';
    }
}
