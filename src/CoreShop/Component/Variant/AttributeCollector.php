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

namespace CoreShop\Component\Variant;

use CoreShop\Component\Variant\Event\VariantAvailabilityEvent;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use CoreShop\Component\Variant\Model\Resolved\ResolvedAttribute;
use CoreShop\Component\Variant\Model\Resolved\ResolvedAttributeGroup;
use CoreShop\Component\Variant\Model\Resolved\ResolvedIndex;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AttributeCollector implements AttributeCollectorInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributesFromVariants(ProductVariantAwareInterface $product, bool $showInList = false): array
    {
        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $variants = [$product];
        } else {
            $variants = $product->getVariants();
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
            $event = $this->eventDispatcher->dispatch(new VariantAvailabilityEvent($product), 'coreshop.attribute.collector.preCondition');

            if (!$event->isConditionMet()) {
                continue;
            }

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

        usort($resolvedGroups, static fn (ResolvedAttributeGroup $a, ResolvedAttributeGroup $b) => $a->getGroup()->getSorting() <=> $b->getGroup()->getSorting());

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

        return $this->getAttributes($product->getVariants(), $showInList);
    }

    public function getIndex(ProductVariantAwareInterface $product)
    {
        $index = [];
        if ($product->getType() === AbstractObject::OBJECT_TYPE_VARIANT) {
            $product = $product->getVariantParent();
        }

        $variants = $product->getVariants();

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
