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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Variant\Model;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

trait ProductVariantTrait
{
    /**
     * @return AttributeInterface[]|null
     */
    abstract public function getAttributes(): ?array;

    public function getVariants(): array
    {
        if ($this instanceof Concrete) {
            $list = $this::getList();
            $list->setCondition('o_path LIKE ?', [$this->getFullPath() . '/%']);
            $list->setObjectTypes([AbstractObject::OBJECT_TYPE_VARIANT]);

            $variants = $list->getObjects();
        } else {
            $variants = $this->getChildren([AbstractObject::OBJECT_TYPE_VARIANT]);
        }

        return $variants;
    }

    public function findAttributeForGroup(AttributeGroupInterface $attributeGroup): ?AttributeInterface
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getAttributeGroup() && $attribute->getAttributeGroup()->getId() === $attributeGroup->getId()) {
                return $attribute;
            }
        }

        return null;
    }

    public function getVariantParent(): Concrete
    {
        $parent = $this;

        while ($parent->getType() === 'variant') {
            if ($parent->getParent() instanceof self) {
                $parent = $parent->getParent();
            }
        }

        return $parent;
    }

    public function findMainVariant(): ?ProductVariantAwareInterface
    {
        /**
         * @var ProductVariantAwareInterface[] $variants
         */
        $variants = $this->getVariants();

        if (!$variants) {
            return null;
        }

        uasort($variants, static function (ProductVariantAwareInterface $a, ProductVariantAwareInterface $b): int {
            $attributesA = $a->getAttributes();
            $attributesB = $b->getAttributes();

            usort(
                $attributesA,
                static fn (
                    AttributeInterface $attributeA,
                    AttributeInterface $attributeB,
                ) => $attributeA->getAttributeGroup()->getSorting() <=> $attributeB->getAttributeGroup()->getSorting(),
            );

            usort(
                $attributesB,
                static fn (
                    AttributeInterface $attributeA,
                    AttributeInterface $attributeB,
                ) => $attributeA->getAttributeGroup()->getSorting() <=> $attributeB->getAttributeGroup()->getSorting(),
            );

            return
                array_map(static fn (AttributeInterface $aa): ?float => $aa->getSorting(), $attributesA)
                <=>
                array_map(static fn (AttributeInterface $bb): ?float => $bb->getSorting(), $attributesB);
        });

        if (count($variants) > 0) {
            return reset($variants);
        }

        return null;
    }
}
