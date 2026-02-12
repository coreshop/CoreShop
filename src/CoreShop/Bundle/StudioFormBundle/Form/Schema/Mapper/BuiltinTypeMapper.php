<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\StudioFormBundle\Form\Schema\Mapper;

use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormTypeMapperInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\UiTypeDescriptor;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

final class BuiltinTypeMapper implements FormTypeMapperInterface
{
    /** @var array<string, string> */
    private const array TYPE_MAP = [
        TextType::class => 'input',
        TextareaType::class => 'textarea',
        IntegerType::class => 'inputNumber',
        NumberType::class => 'inputNumber',
        CheckboxType::class => 'switch',
        HiddenType::class => 'hidden',
    ];

    public function supports(FormInterface $field): bool
    {
        return $this->resolveWidget($field) !== null;
    }

    public function map(FormInterface $field, array $options): UiTypeDescriptor
    {
        $widget = $this->resolveWidget($field);

        if ($widget === 'select') {
            return $this->mapChoiceType($field, $options);
        }

        if ($widget === 'collection') {
            return $this->mapCollectionType($options);
        }

        return new UiTypeDescriptor($widget ?? 'input');
    }

    private function resolveWidget(FormInterface $field): ?string
    {
        $resolvedType = $field->getConfig()->getType();

        // Walk up the type hierarchy to find a matching mapper
        while ($resolvedType !== null) {
            $typeName = $resolvedType->getInnerType()::class;

            if (isset(self::TYPE_MAP[$typeName])) {
                return self::TYPE_MAP[$typeName];
            }

            if ($typeName === ChoiceType::class) {
                return 'select';
            }

            if ($typeName === CollectionType::class) {
                return 'collection';
            }

            $resolvedType = $resolvedType->getParent();
        }

        return null;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function mapChoiceType(FormInterface $field, array $options): UiTypeDescriptor
    {
        $choiceOptions = [];

        if (isset($options['multiple']) && $options['multiple']) {
            $choiceOptions['multiple'] = true;
        }

        // Extract resolved choices
        $choices = [];
        foreach ($field->createView()->vars['choices'] ?? [] as $choice) {
            if (is_object($choice) && property_exists($choice, 'value') && property_exists($choice, 'label')) {
                $choices[] = [
                    'value' => $choice->value,
                    'label' => (string) $choice->label,
                ];
            }
        }

        if (count($choices) > 0) {
            $choiceOptions['choices'] = $choices;
        }

        return new UiTypeDescriptor('select', $choiceOptions);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function mapCollectionType(array $options): UiTypeDescriptor
    {
        $collectionOptions = [];

        if (isset($options['allow_add'])) {
            $collectionOptions['allowAdd'] = (bool) $options['allow_add'];
        }

        if (isset($options['allow_delete'])) {
            $collectionOptions['allowDelete'] = (bool) $options['allow_delete'];
        }

        return new UiTypeDescriptor('collection', $collectionOptions);
    }
}
