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

namespace CoreShop\Bundle\VariantBundle\Service;

use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Tool;

class VariantGeneratorService implements VariantGeneratorServiceInterface
{
    public function generateVariants(array $combinations, ProductVariantAwareInterface $product): array
    {
        $variants = [];
        foreach ($combinations as $attributeIds) {
            $variant = $this->generateVariant($attributeIds, $product);

            if ($variant) {
                $variants[] = $variant;
            }
        }

        return $variants;
    }

    public function generateVariant(array $attributeIds, ProductVariantAwareInterface $product): ?ProductVariantAwareInterface
    {
        if (!$product instanceof DataObject\Concrete) {
            return null;
        }

        if (count($product->getAllowedAttributeGroups()) !== count($attributeIds)) {
            return null;
        }

        $existingVariants = $product::getList();
        $existingVariants->setCondition('path LIKE \'' . $product->getFullPath() . '/%\'');
        $attributeCondition = implode(' AND ', array_map(static function ($id) {
            return 'attributes LIKE "%object|' . $id . '%"';
        }, $attributeIds));
        $existingVariants->addConditionParam($attributeCondition);
        $existingVariants->setLimit(1);
        $existingVariants->setUnpublished(true);

        // there is already a variant with the given attributes
        if ($existingVariants->count()) {
            return null;
        }

        /**
         * @var ProductVariantAwareInterface $variant
         */
        $variant = new ($product::class)();

        $attributes = array_filter(array_map(static function ($attributeId) {
            $attribute = DataObject::getById($attributeId);

            return $attribute instanceof AttributeInterface ? $attribute : null;
        }, $attributeIds));

        $key = implode(' - ', array_map(static function (AttributeInterface $attribute) {
            return $attribute->getKey();
        }, $attributes));

        foreach (Tool::getValidLanguages() as $language) {
            $name = implode(' ', array_map(static function (AttributeInterface $attribute) use ($language) {
                return $attribute->getName($language);
            }, $attributes));

            $variant->setName(sprintf('%s %s', $product->getName($language), $name), $language);
        }

        $variant->setKey($key);
        $variant->setParent($product);
        $variant->setPublished(false);
        $variant->setType(AbstractObject::OBJECT_TYPE_VARIANT);
        $variant->setAttributes($attributes);
        $variant->save();

        return $variant;
    }

    public function generateCombinations(array $groupedAttributes, array $currentCombination, int $groupIndex, array &$combinations): void
    {
        if ($groupIndex >= count($groupedAttributes)) {
            $combinations[] = $currentCombination;

            return;
        }

        $currentGroup = array_values($groupedAttributes)[$groupIndex];

        foreach ($currentGroup as $attribute) {
            $currentCombination[] = $attribute;
            $this->generateCombinations($groupedAttributes, $currentCombination, $groupIndex + 1, $combinations);
            array_pop($currentCombination);
        }
    }
}
