<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\Variant\Model\Resolved;

use Doctrine\Common\Collections\ArrayCollection;

class ResolvedIndex
{
    private ArrayCollection $attributes;
    private string $url;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    public function getAttributes(): ArrayCollection
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = new ArrayCollection($attributes);
    }

    public function addAttribute(?ResolvedAttributeGroup $attributeGroup): void
    {
        if ($attributeGroup) {
            $attributes = $attributeGroup->getAttributes();
            $attribute = reset($attributes);
            if ($attribute instanceof ResolvedAttribute) {
                $this->attributes->set($attributeGroup->getGroup()->getId(), $attribute->getAttribute()->getId());
            }
        }
    }

    public function removeAttribute(ResolvedAttributeGroup $attributeGroup): void
    {
        if ($this->hasAttribute($attributeGroup)) {
            $this->attributes->removeElement($attributeGroup);
        }
    }

    public function hasAttribute(ResolvedAttributeGroup $attributeGroup): bool
    {
        return $this->attributes->containsKey($attributeGroup->getGroup()->getId());
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}