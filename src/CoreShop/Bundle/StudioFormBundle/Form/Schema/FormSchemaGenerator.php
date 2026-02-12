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

namespace CoreShop\Bundle\StudioFormBundle\Form\Schema;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class FormSchemaGenerator
{
    /** @var FormSchemaEnricherInterface[] */
    private array $enrichers = [];

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly FormTypeMapperRegistry $mapperRegistry,
    ) {
    }

    public function addEnricher(FormSchemaEnricherInterface $enricher): void
    {
        $this->enrichers[] = $enricher;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function generate(string $formTypeClass, array $options = []): FormSchema
    {
        $form = $this->formFactory->create($formTypeClass, null, array_merge([
            'csrf_protection' => false,
        ], $options));

        $schema = $this->buildSchema($form);

        foreach ($this->enrichers as $enricher) {
            if ($enricher->supports($formTypeClass)) {
                $schema = $enricher->enrich($schema, $formTypeClass);
            }
        }

        return $schema;
    }

    private function buildSchema(FormInterface $form): FormSchema
    {
        $blockPrefix = $form->getConfig()->getType()->getBlockPrefix();
        $fields = [];

        foreach ($form as $child) {
            $fieldSchema = $this->buildFieldSchema($child);
            if ($fieldSchema !== null) {
                $fields[] = $fieldSchema;
            }
        }

        return new FormSchema(
            blockPrefix: $blockPrefix,
            fields: $fields,
        );
    }

    private function buildFieldSchema(FormInterface $field): ?FieldSchema
    {
        $blockPrefix = $field->getConfig()->getType()->getBlockPrefix();
        $required = $field->isRequired();

        // Check if this is a compound field with children (like translations)
        if ($field->getConfig()->getCompound() && count($field) > 0) {
            $uiType = $this->mapperRegistry->resolve($field);

            if ($uiType === null) {
                // For compound types like translations, use the blockPrefix as widget hint
                $uiType = new UiTypeDescriptor($blockPrefix);
            }

            // For translations: build children from the first locale entry
            // instead of listing all locales as separate fields
            if (!empty($uiType->options['childrenFromFirstEntry'])) {
                $firstChild = null;
                foreach ($field as $child) {
                    $firstChild = $child;
                    break;
                }
                $childSchema = $firstChild !== null ? $this->buildSchema($firstChild) : new FormSchema($blockPrefix);
            } else {
                $childSchema = $this->buildSchema($field);
            }

            return new FieldSchema(
                name: $field->getName(),
                blockPrefix: $blockPrefix,
                required: $required,
                uiType: $uiType,
                children: $childSchema,
            );
        }

        // Try to resolve through mapper registry
        $uiType = $this->mapperRegistry->resolve($field);

        if ($uiType === null) {
            // Fallback: walk up parent types to find a mapper
            $uiType = $this->resolveFromParentTypes($field);
        }

        if ($uiType === null) {
            // Skip compound fields that no mapper can handle (e.g., conditions/actions
            // collections) - these have dedicated rendering in their own tabs.
            if ($field->getConfig()->getCompound()) {
                return null;
            }

            // Last resort fallback for simple fields
            $uiType = new UiTypeDescriptor('input');
        }

        return new FieldSchema(
            name: $field->getName(),
            blockPrefix: $blockPrefix,
            required: $required,
            uiType: $uiType,
        );
    }

    private function resolveFromParentTypes(FormInterface $field): ?UiTypeDescriptor
    {
        $resolvedType = $field->getConfig()->getType();
        $parent = $resolvedType->getParent();

        while ($parent !== null) {
            // Create a temporary form to check mapper support
            $uiType = $this->mapperRegistry->resolve($field);
            if ($uiType !== null) {
                return $uiType;
            }

            $parent = $parent->getParent();
        }

        return null;
    }
}
