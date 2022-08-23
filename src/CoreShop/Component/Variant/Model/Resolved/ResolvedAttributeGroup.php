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

use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ResolvedAttributeGroup
{
    private AttributeGroupInterface $group;
    private ArrayCollection $attributes;
    private string $type;
    private int $selected = 0;

    public function __construct()
    {
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
        usort($attributes, static fn(ResolvedAttribute $a, ResolvedAttribute $b) => $a->getAttribute()->getSorting() <=> $b->getAttribute()->getSorting());

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
