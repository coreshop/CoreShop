<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Variant\Model\Resolved;

use Doctrine\Common\Collections\ArrayCollection;

class ResolvedIndex
{
    private ArrayCollection $attributes;

    private string $url;

    public function __construct(
        ) {
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
