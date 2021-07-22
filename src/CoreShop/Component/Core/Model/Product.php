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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Product\Model\Product as BaseProduct;

abstract class Product extends BaseProduct implements ProductInterface
{
    public function getInventoryName(): ?string
    {
        return $this->getName();
    }

    public function isInStock(): bool
    {
        return 0 < $this->getOnHand();
    }

    public function getMetaTitle($language = null): ?string
    {
        return $this->getPimcoreMetaTitle($language) ?: $this->getName($language);
    }

    public function getMetaDescription($language = null): ?string
    {
        return $this->getPimcoreMetaDescription($language) ?: $this->getShortDescription($language);
    }

    public function getOGTitle($language = null): ?string
    {
        return $this->getMetaTitle($language);
    }

    public function getOGDescription($language = null): ?string
    {
        return $this->getMetaDescription($language);
    }

    public function getOGType(): ?string
    {
        return 'product';
    }

    public function getIndexableEnabled(IndexInterface $index): bool
    {
        return $this->getActive() && $this->getPublished();
    }

    public function getIndexable(IndexInterface $index): bool
    {
        return $this->getIndexableEnabled($index);
    }

    public function getIndexableName(IndexInterface $index, string $language): string
    {
        return $this->getName($language);
    }
}
