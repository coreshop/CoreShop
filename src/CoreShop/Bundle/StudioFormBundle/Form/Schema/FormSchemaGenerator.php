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

use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

final class FormSchemaGenerator
{
    /** @var FormSchemaEnricherInterface[] */
    private array $enrichers = [];

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
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

        $view = $form->createView();
        $schema = $this->serializeView($view);

        foreach ($this->enrichers as $enricher) {
            if ($enricher->supports($formTypeClass)) {
                $schema = $enricher->enrich($schema, $formTypeClass);
            }
        }

        return $schema;
    }

    private function serializeView(FormView $view): FormSchema
    {
        $blockPrefix = $this->getBlockPrefix($view);
        $fields = [];

        foreach ($view->children as $childView) {
            $fields[] = $this->serializeFieldView($childView);
        }

        return new FormSchema(
            blockPrefix: $blockPrefix,
            fields: $fields,
        );
    }

    private function serializeFieldView(FormView $childView): FieldSchema
    {
        $blockPrefixes = $childView->vars['block_prefixes'] ?? [];
        // Strip the unique block prefix (last element, e.g. '_cart_price_rule_name')
        array_pop($blockPrefixes);

        $field = new FieldSchema(
            name: $childView->vars['name'],
            blockPrefixes: $blockPrefixes,
            required: $childView->vars['required'] ?? false,
            label: $childView->vars['label'] ?? null,
            disabled: $childView->vars['disabled'] ?? false,
        );

        // Choice fields: serialize choices, multiple, expanded from FormView vars
        if (isset($childView->vars['choices'])) {
            $field->choices = $this->serializeChoices($childView->vars['choices']);
            $field->multiple = $childView->vars['multiple'] ?? false;
            $field->expanded = $childView->vars['expanded'] ?? false;
        }

        // Extract extra vars (e.g. autocomplete_class set by custom types in buildView)
        $field->extra = $this->extractExtraVars($childView);

        // Collection type metadata
        if (isset($childView->vars['allow_add'])) {
            $field->extra['allow_add'] = $childView->vars['allow_add'];
            $field->extra['allow_delete'] = $childView->vars['allow_delete'] ?? false;
        }

        // Single prototype (standard Symfony CollectionType)
        if (isset($childView->vars['prototype']) && $childView->vars['prototype'] instanceof FormView) {
            $field->prototype = $this->serializeView($childView->vars['prototype']);
        }

        // Multiple prototypes (CoreShop pattern: one per condition/action type)
        if (isset($childView->vars['prototypes']) && is_array($childView->vars['prototypes'])) {
            $field->prototypes = [];
            foreach ($childView->vars['prototypes'] as $type => $protoView) {
                if ($protoView instanceof FormView) {
                    $field->prototypes[$type] = $this->serializeView($protoView);
                }
            }
        }

        // Recursively serialize compound children
        if (count($childView->children) > 0) {
            $field->children = $this->serializeView($childView);
        }

        return $field;
    }

    private function getBlockPrefix(FormView $view): string
    {
        $blockPrefixes = $view->vars['block_prefixes'] ?? [];
        // The second-to-last element is the type's block prefix
        // e.g. ['form', 'coreshop_cart_price_rule', '_cart_price_rule'] -> 'coreshop_cart_price_rule'
        if (count($blockPrefixes) >= 2) {
            return $blockPrefixes[count($blockPrefixes) - 2];
        }

        return $blockPrefixes[0] ?? 'form';
    }

    /**
     * @param ChoiceView[]|ChoiceGroupView[] $choices
     *
     * @return array<array{value: string|int, label: string}>
     */
    private function serializeChoices(array $choices): array
    {
        $result = [];

        foreach ($choices as $choice) {
            if ($choice instanceof ChoiceGroupView) {
                // Flatten choice groups into single list with group info
                foreach ($choice as $groupedChoice) {
                    if ($groupedChoice instanceof ChoiceView) {
                        $result[] = [
                            'value' => $groupedChoice->value,
                            'label' => (string) $groupedChoice->label,
                            'group' => $choice->label,
                        ];
                    }
                }
            } elseif ($choice instanceof ChoiceView) {
                $result[] = [
                    'value' => $choice->value,
                    'label' => (string) $choice->label,
                ];
            }
        }

        return $result;
    }

    /**
     * Extract custom extra vars that form types set in buildView().
     *
     * Standard FormView vars (id, name, full_name, required, etc.) are excluded.
     * Only non-standard vars that form types add (e.g. autocomplete_class) are included.
     *
     * @return array<string, mixed>
     */
    private function extractExtraVars(FormView $view): array
    {
        $standardVars = [
            'id', 'name', 'full_name', 'value', 'data', 'block_prefixes',
            'required', 'disabled', 'label', 'label_attr', 'label_format',
            'label_html', 'label_translation_parameters', 'help', 'help_attr',
            'help_html', 'help_translation_parameters', 'compound', 'method',
            'action', 'submitted', 'attr', 'row_attr', 'errors', 'valid',
            'form', 'translation_domain', 'unique_block_prefix', 'priority',
            // Choice type vars
            'choices', 'multiple', 'expanded', 'preferred_choices',
            'choice_translation_domain', 'placeholder', 'placeholder_in_choices',
            'placeholder_attr', 'separator',
            // Collection type vars
            'allow_add', 'allow_delete', 'prototype', 'prototype_name', 'prototypes',
            // Internal vars
            'multipart', 'cache_key', 'is_selected',
        ];

        $extra = [];
        foreach ($view->vars as $key => $value) {
            if (!in_array($key, $standardVars, true) && !str_starts_with($key, '_')) {
                // Only include scalar or simple array values
                if (is_scalar($value) || is_array($value)) {
                    $extra[$key] = $value;
                }
            }
        }

        return $extra;
    }
}
