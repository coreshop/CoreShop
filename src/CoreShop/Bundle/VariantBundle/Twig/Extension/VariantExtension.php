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

namespace CoreShop\Bundle\VariantBundle\Twig\Extension;

use CoreShop\Component\Variant\AttributeCollectorInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class VariantExtension extends AbstractExtension
{
    public function __construct(
        protected AttributeCollectorInterface $attributeCollector,
        protected NormalizerInterface $serializer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_variant_attribute_list', [$this->attributeCollector, 'getAttributes']),
            new TwigFunction('coreshop_variant_attribute_list_object', [$this->attributeCollector, 'getAttributesFromObject']),
            new TwigFunction('coreshop_variant_attribute_list_variants', [$this->attributeCollector, 'getAttributesFromVariants']),
            new TwigFunction('coreshop_variant_index', [$this->attributeCollector, 'getIndex']),
            new TwigFunction('coreshop_variant_selected', [$this, 'setSelected']),
            new TwigFunction('coreshop_variant_serialize_groups', [$this, 'serializeGroups']),
            new TwigFunction('coreshop_variant_serialize_index', [$this, 'serializeIndex']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_variant_attribute_list', [$this->attributeCollector, 'getAttributes']),
            new TwigFilter('coreshop_variant_attribute_list_object', [$this->attributeCollector, 'getAttributesFromObject']),
            new TwigFilter('coreshop_variant_attribute_list_variants', [$this->attributeCollector, 'getAttributesFromVariants']),
            new TwigFilter('coreshop_variant_index', [$this->attributeCollector, 'getIndex']),
            new TwigFilter('coreshop_variant_selected', [$this, 'setSelected']),
            new TwigFilter('coreshop_variant_serialize_groups', [$this, 'serializeGroups']),
            new TwigFilter('coreshop_variant_serialize_index', [$this, 'serializeIndex']),
        ];
    }

    public function setSelected(array $attributeGroups, ProductVariantAwareInterface $product)
    {
        foreach ($attributeGroups as $attributeGroup) {
            foreach ($product->getAttributes() as $attribute) {
                if ($attribute->getAttributeGroup() !== $attributeGroup->getGroup()) {
                    continue;
                }

                $attributeGroup->setSelected($attribute->getId());
            }
        }

        return $attributeGroups;
    }

    public function serializeGroups(array $attributeGroups, array $groups = ['coreshop'])
    {
        return $this->serializer->normalize($attributeGroups, 'json', [
            'groups' => $groups,
            AbstractNormalizer::CALLBACKS => [
                'valueColor' => static function (?string $innerObject) {
                    return $innerObject;
                },
            ],
        ]);
    }

    public function serializeIndex(array $index, array $groups = ['coreshop'])
    {
        return $this->serializer->normalize($index, 'json', $groups);
    }
}
