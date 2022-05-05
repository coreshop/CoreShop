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

namespace CoreShop\Component\Variant\Model;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

trait ProductVariantTrait
{
    /**
     * @return AttributeInterface[]|null
     */
    abstract public function getAttributes(): ?array;

    public function findAttributeForGroup(AttributeGroupInterface $attributeGroup): ?AttributeInterface
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getAttributeGroup() && $attribute->getAttributeGroup()->getId() === $attributeGroup->getId(
                )) {
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
        $variants = $this->getChildren([AbstractObject::OBJECT_TYPE_VARIANT]);

        if (!$variants) {
            return null;
        }

        uasort($variants, static function (ProductVariantAwareInterface $a, ProductVariantAwareInterface $b): int {
            $attributesA = $a->getAttributes();
            $attributesB = $b->getAttributes();

            usort($attributesA,
                static fn(
                    AttributeInterface $attributeA,
                    AttributeInterface $attributeB
                ) => $attributeA->getAttributeGroup()->getSorting() <=> $attributeB->getAttributeGroup()->getSorting()
            );

            usort($attributesB,
                static fn(
                    AttributeInterface $attributeA,
                    AttributeInterface $attributeB
                ) => $attributeA->getAttributeGroup()->getSorting() <=> $attributeB->getAttributeGroup()->getSorting()
            );

            return
                array_map(static fn(AttributeInterface $aa): ?float => $aa->getSorting(), $attributesA)
                <=>
                array_map(static fn(AttributeInterface $bb): ?float => $bb->getSorting(), $attributesB);
        });

        if (count($variants) > 0) {
            return $variants[0];
        }

        return null;
    }
}
