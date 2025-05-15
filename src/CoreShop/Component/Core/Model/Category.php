<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
