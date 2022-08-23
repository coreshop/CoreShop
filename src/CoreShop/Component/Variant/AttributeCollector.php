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

namespace CoreShop\Component\Variant;

use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use CoreShop\Component\Variant\Model\Resolved\ResolvedAttribute;
use CoreShop\Component\Variant\Model\Resolved\ResolvedAttributeGroup;
use CoreShop\Component\Variant\Model\Resolved\ResolvedIndex;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

class AttributeCollector implements AttributeCollectorInterface
{
    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributesFromVariants(ProductVariantAwareInterface $product, bool $showInList = false): array
    {
        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $variants = [$product];
        } else {
            $variants = $product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT]);
        }

        return $this->getAttributes($variants, $showInList);
    }

    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributes(array $products, bool $showInList = false): array
    {
        $resolvedGroups = [];
        foreach ($products as $product) {
            /**
             * @var AttributeInterface $attribute
             */
            foreach ($product->getAttributes() as $attribute) {
                /**
                 * @var AttributeGroupInterface|null $attributeGroup
                 */
                $attributeGroup = $attribute->getAttributeGroup();

                if (!$attributeGroup) {
                    continue;
                }

                if ($showInList && !$attributeGroup->getShowInList()) {
                    continue;
                }

                if (!isset($resolvedGroups[$attributeGroup->getId()])) {
                    $group = new ResolvedAttributeGroup();
                    $group->setGroup($attributeGroup);
                    $group->setType(get_class($attribute));

                    $resolvedGroups[$attributeGroup->getId()] = $group;
                } else {
                    $group = $resolvedGroups[$attributeGroup->getId()];
                }

                $resolvedAttribute = new ResolvedAttribute($attribute);
                if (!$group->hasAttribute($resolvedAttribute)) {
                    $group->addAttribute($resolvedAttribute);
                } else {
                    $resolvedAttribute = $group->getAttribute($attribute->getId());
                }

                $resolvedAttribute->addProduct($product);

                $group->addAttribute($resolvedAttribute);
            }
        }

        usort($resolvedGroups, static fn(ResolvedAttributeGroup $a, ResolvedAttributeGroup $b) => $a->getGroup()->getSorting() <=> $b->getGroup()->getSorting());

        return $resolvedGroups;
    }

    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributesFromObject(ProductVariantAwareInterface $product, bool $showInList = false): array
    {
        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $product = $product->getVariantParent();
        }
        $variants = $product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT]);

        return $this->getAttributes($variants, $showInList);
    }

    public function getIndex(ProductVariantAwareInterface $product)
    {
        $index = [];
        if ($product->getType() === AbstractObject::OBJECT_TYPE_VARIANT) {
            $product = $product->getVariantParent();
        }

        $variants = $product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT]);

        foreach ($variants as $variant) {
            if (!$variant instanceof ProductVariantAwareInterface) {
                continue;
            }

            $attributeGroups = $this->getAttributes([$variant], false);
            $indexElement = new ResolvedIndex();

            /**
             * @psalm-var Concrete&ProductVariantAwareInterface $variant
             */
            $indexElement->setUrl($variant->getClass()->getLinkGenerator()->generate($variant));
            foreach ($attributeGroups as $attributeGroup) {
                $attribute = $attributeGroup->getAttributes();
                $attribute = reset($attribute);
                $indexElement->addAttribute($attributeGroup);
            }
            $index[$variant->getId()] = $indexElement;
        }

        return $index;
    }
}
