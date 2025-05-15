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

namespace CoreShop\Component\Variant\Model\Resolved;

use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ResolvedAttributeGroup
{
    private AttributeGroupInterface $group;

    private ArrayCollection $attributes;

    private string $type;

    private int $selected = 0;

    public function __construct(
        ) {
        $this->attributes = new ArrayCollection();
    }

    public function getGroup(): AttributeGroupInterface
    {
        return $this->group;
    }

    public function setGroup(AttributeGroupInterface $group): void
    {
        $this->group = $group;
    }

    public function getAttributes(): array
    {
        $attributes = $this->attributes->toArray();
        usort($attributes, static fn (ResolvedAttribute $a, ResolvedAttribute $b) => $a->getAttribute()->getSorting() <=> $b->getAttribute()->getSorting());

        return $attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = new ArrayCollection($attributes);
    }

    public function addAttribute(ResolvedAttribute $attribute): void
    {
        if (!$this->hasAttribute($attribute)) {
            $this->attributes->set($attribute->getAttribute()->getId(), $attribute);
        }
    }

    public function removeAttribute(ResolvedAttribute $attribute): void
    {
        if ($this->hasAttribute($attribute)) {
            $this->attributes->removeElement($attribute);
        }
    }

    public function hasAttribute(ResolvedAttribute $attribute): bool
    {
        return $this->attributes->containsKey($attribute->getAttribute()->getId());
    }

    public function getAttribute(int $key): ResolvedAttribute
    {
        return $this->attributes->get($key);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSelected(): int
    {
        return $this->selected;
    }

    public function setSelected(int $selected): void
    {
        $this->selected = $selected;
    }
}
