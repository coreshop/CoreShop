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

namespace CoreShop\Bundle\VariantBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\VariantBundle\Messenger\CreateVariantMessage;
use CoreShop\Bundle\VariantBundle\Service\VariantGeneratorService;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @psalm-suppress InternalClass
 */
class VariantController extends AdminController
{
    public function getAttributesAction(Request $request)
    {
        $id = $this->getParameterFromRequest($request, 'id');

        if (!$id) {
            throw new \InvalidArgumentException('no product id given');
        }

        $product = DataObject::getById($id);

        if (!$product instanceof ProductVariantAwareInterface) {
            throw new NotFoundHttpException('no product found');
        }

        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $product = $product->getVariantParent();
        }

        $attributeGroups = $product->getAllowedAttributeGroups();

        $data = array_map(static function (AttributeGroupInterface $group) {
            return [
                'text' => sprintf('%s (ID: %s)', $group->getKey(), $group->getId()),
                'sorting' => $group->getSorting(),
                'leaf' => false,
                'iconCls' => 'pimcore_icon_object',
                'data' => array_map(static function (AttributeInterface $attribute) use ($group) {
                    return [
                        'text' => sprintf('%s (ID: %s)', $attribute->getKey(), $attribute->getId()),
                        'id' => $attribute->getId(),
                        'group_id' => $group->getId(),
                        'sorting' => $attribute->getSorting(),
                        'leaf' => true,
                        'checked' => false,
                        'iconCls' => 'pimcore_icon_object',
                    ];
                }, $group->getAttributes()),
            ];
        }, $attributeGroups);

        return $this->json(
            [
                'success' => true,
                'data' => $data,
            ],
        );
    }

    public function generateVariantsAction(
        Request $request,
        VariantGeneratorService $variantGeneratorService,
        MessageBusInterface $messageBus,
        TranslatorInterface $translator,
    ) {
        $id = $this->getParameterFromRequest($request, 'id');
        $attributes = $this->getParameterFromRequest($request, 'attributes');

        if (!$id) {
            throw new \InvalidArgumentException('no product id given');
        }

        if (!$attributes) {
            throw new \InvalidArgumentException('no attributes given');
        }

        $product = DataObject::getById($id);

        if (!$product instanceof ProductVariantAwareInterface) {
            throw new NotFoundHttpException('no product found');
        }

        if (AbstractObject::OBJECT_TYPE_VARIANT === $product->getType()) {
            $product = $product->getVariantParent();
        }

        $combinations = [];
        $variantGeneratorService->generateCombinations($attributes, [], 0, $combinations);

        foreach ($combinations as $attributeIds) {
            /**
             * @psalm-suppress InternalMethod
             */
            $messageBus->dispatch(
                new CreateVariantMessage($product->getId(), $attributeIds, $this->getAdminUser()?->getId()),
            );
        }

        return $this->json(
            [
                'success' => true,
                'message' => $translator->trans('coreshop.variant_generator.generate_in_background', [], 'admin'),
            ],
        );
    }
}
