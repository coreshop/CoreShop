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

namespace CoreShop\Bundle\VariantBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @psalm-suppress InternalClass
 */
class VariantController extends AdminController
{
    private function mapData($object) {

    }

    public function getAttributes(Request $request)
    {
        $id = $this->getParameterFromRequest($request, 'id');

        if(!$id) {
            throw new \InvalidArgumentException('no product id given');
        }

        $product = DataObject::getById($id);

        if(!$product instanceof ProductVariantAwareInterface) {
            throw new NotFoundHttpException('no product found');
        }

        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $product = $product->getVariantParent();
        }

        $attributeGroups = $product->getAllowedAttributeGroups();

        $data = array_map(static function(AttributeGroupInterface $group) {
            return [
                'text' => sprintf('%s (ID: %s)', $group->getKey(), $group->getId()),
                'sorting' => $group->getSorting(),
                'leaf' => false,
                'iconCls' => 'pimcore_icon_object',
                'data' => array_map(static function(AttributeInterface $attribute) use ($group) {
                    return [
                        'text' => sprintf('%s (ID: %s)', $attribute->getKey(), $attribute->getId()),
                        'id' => $attribute->getId(),
                        'group_id' => $group->getId(),
                        'sorting' => $attribute->getSorting(),
                        'leaf' => true,
                        'checked' => false,
                        'iconCls' => 'pimcore_icon_object',
                    ];
                }, $group->getAttributes())
            ];
        }, $attributeGroups);

        return $this->json(
            [
                'success' => true,
                'data' => $data
            ]
        );
    }
    public function generateVariantsAction(Request $request)
    {
        $id = $this->getParameterFromRequest($request, 'id');
        $attributes = $this->getParameterFromRequest($request, 'attributes');

        if(!$id) {
            throw new \InvalidArgumentException('no product id given');
        }

        if(!$attributes) {
            throw new \InvalidArgumentException('no attributes given');
        }

        $product = DataObject::getById($id);

        if(!$product instanceof ProductVariantAwareInterface) {
            throw new NotFoundHttpException('no product found');
        }

        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $product = $product->getVariantParent();
        }

        $groupedAttributes = [];
        foreach ($attributes as $attribute) {
            $groupedAttributes[$attribute['group_id']][] = $attribute;
        }

        $combinations = [];
        $this->generateCombinations($groupedAttributes, [], 0, $combinations);
        $this->generateVariants($combinations, $product);

        $data = [];

        return $this->json(
            [
                'success' => true,
                'data' => $data
            ]
        );
    }

    protected function generateVariants(array $combinations, ProductVariantAwareInterface $product): array
    {
        $variants = [];
        foreach($combinations as $combinationAttribute) {
            $attributes = array_map(static function($combination) {
                return DataObject::getById($combination['id']);
            }, $combinationAttribute);

            // TODO: search variant by attributes
            $variants = DataObject\CoreShopProduct::getByAttributes(array_map(static function($attribute) {

            }, $attributes), 1);


            // TODO
            //$variants = $product->getChildren([DataObject::OBJECT_TYPE_VARIANT]);
            //$variants->addConditionParam('attributes')
            $variant = null;

            if(!$variant instanceof ProductVariantAwareInterface) {
                $class = get_class($product);
                /**
                 * @var ProductVariantAwareInterface $variant
                 */
                $variant = new $class();
            }

            $key = implode(' ', array_map(static function(AttributeInterface $attribute) {
                return $attribute->getKey();
            }, $attributes));

            foreach(Tool::getValidLanguages() as $language) {
                $name = implode(' ', array_map(static function(AttributeInterface $attribute) {
                    return $attribute->getName();
                }, $attributes));

                $variant->setName(sprintf('%s %s', $product->getName(), $name), $language);
            }

            $variant->setKey($key);
            $variant->setParent($product);
            $variant->setPublished(false);
            $variant->setType(DataObject::OBJECT_TYPE_VARIANT);
            $variant->setAttributes($attributes);
            $variant->save();
            $variants[] = $variant;
        }

        return $variants;
    }

    private function generateCombinations($groupedAttributes, $currentCombination, $groupIndex, &$combinations): void
    {
        if ($groupIndex >= count($groupedAttributes)) {
            // Base case: reached the end of groups, add the combination to the result
            $combinations[] = $currentCombination;
            return;
        }

        $currentGroup = array_values($groupedAttributes)[$groupIndex];

        foreach ($currentGroup as $attribute) {
            // Include the current attribute in the combination
            $currentCombination[] = $attribute;

            // Recur to the next group
            $this->generateCombinations($groupedAttributes, $currentCombination, $groupIndex + 1, $combinations);

            // Backtrack: remove the current attribute from the combination
            array_pop($currentCombination);
        }
    }

}
