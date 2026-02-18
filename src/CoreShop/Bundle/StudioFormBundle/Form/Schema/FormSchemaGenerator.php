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
use Symfony\Contracts\Translation\TranslatorInterface;

final class FormSchemaGenerator
{
    /** @var FormSchemaEnricherInterface[] */
    private array $enrichers = [];

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
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

        // Collect children and sort by Symfony's priority option (higher = first).
        $children = iterator_to_array($view->children ?? []);
        usort($children, static function (FormView $a, FormView $b): int {
            $pa = $a->vars['priority'] ?? 0;
            $pb = $b->vars['priority'] ?? 0;

            return $pb <=> $pa;
        });

        $fields = [];
        foreach ($children as $childView) {
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
        if (!is_array($blockPrefixes)) {
            $blockPrefixes = [];
        }
        // Strip only the unique runtime block prefix (e.g. '_cart_price_rule_name').
        // Keep real type prefixes like "number" or "coreshop_*" intact.
        $blockPrefixes = $this->stripUniqueBlockPrefix($blockPrefixes);

        $label = $childView->vars['label'] ?? null;
        // Symfony allows label = false (meaning "don't display a label"); treat as null.
        if (!is_string($label)) {
            $label = null;
        }

        // Translate label server-side so the frontend always receives human-readable text
        if ($label !== null) {
            $translationDomain = $childView->vars['translation_domain'] ?? null;
            $translated = $this->translator->trans($label, [], $translationDomain);
            // If the form's own domain didn't resolve, try the 'studio' domain
            if ($translated === $label && $translationDomain !== 'studio') {
                $studioTranslated = $this->translator->trans($label, [], 'studio');
                if ($studioTranslated !== $label) {
                    $translated = $studioTranslated;
                }
            }
            if ($translated !== $label) {
                $label = $translated;
            }
        }

        $field = new FieldSchema(
            name: $childView->vars['name'],
            blockPrefixes: $blockPrefixes,
            required: $childView->vars['required'] ?? false,
            label: $label,
            disabled: $childView->vars['disabled'] ?? false,
        );

        // Choice fields: serialize choices, multiple, expanded from FormView vars
        if (isset($childView->vars['choices']) && is_array($childView->vars['choices'])) {
            $choiceTranslationDomain = $childView->vars['choice_translation_domain'] ?? $childView->vars['translation_domain'] ?? null;
            $field->choices = $this->serializeChoices($childView->vars['choices'], $choiceTranslationDomain);
            $field->multiple = $childView->vars['multiple'] ?? false;
            $field->expanded = $childView->vars['expanded'] ?? false;
        }

        // Extract extra vars (e.g. relation_class set by custom types in buildView)
        $field->extra = $this->extractExtraVars($childView);

        // Preserve "multiple" for PimcoreRelationType so frontend can choose
        // between ManyToOne and ManyToMany relation widgets.
        if (in_array('coreshop_pimcore_relation', $blockPrefixes, true) && isset($childView->vars['multiple'])) {
            $field->extra['multiple'] = (bool) $childView->vars['multiple'];
        }

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
        if (count($childView->children ?? []) > 0) {
            $field->children = $this->serializeView($childView);
        }

        return $field;
    }

    private function getBlockPrefix(FormView $view): string
    {
        $blockPrefixes = $view->vars['block_prefixes'] ?? [];
        if (!is_array($blockPrefixes) || count($blockPrefixes) === 0) {
            return 'form';
        }

        // Root forms can be either:
        // - ['form', 'coreshop_x', '_coreshop_x'] (with unique suffix)
        // - ['form', 'coreshop_x'] (without unique suffix)
        // Always use the most specific non-unique prefix.
        $blockPrefixes = $this->stripUniqueBlockPrefix($blockPrefixes);

        return $blockPrefixes[count($blockPrefixes) - 1] ?? 'form';
    }

    /**
     * @param string[] $blockPrefixes
     *
     * @return string[]
     */
    private function stripUniqueBlockPrefix(array $blockPrefixes): array
    {
        $last = end($blockPrefixes);
        if (is_string($last) && str_starts_with($last, '_')) {
            array_pop($blockPrefixes);
        }

        return $blockPrefixes;
    }

    /**
     * @param ChoiceView[]|ChoiceGroupView[] $choices
     * @param string|false|null              $translationDomain
     *
     * @return array<array{value: string|int, label: string}>
     */
    private function serializeChoices(array $choices, string|false|null $translationDomain = null): array
    {
        $result = [];

        foreach ($choices as $choice) {
            if ($choice instanceof ChoiceGroupView) {
                // Flatten choice groups into single list with group info
                foreach ($choice as $groupedChoice) {
                    if ($groupedChoice instanceof ChoiceView) {
                        $result[] = [
                            'value' => $this->normalizeChoiceValue($groupedChoice->value),
                            'label' => $this->translateChoiceLabel((string) $groupedChoice->label, $translationDomain),
                            'group' => $this->translateChoiceLabel($choice->label, $translationDomain),
                        ];
                    }
                }
            } elseif ($choice instanceof ChoiceView) {
                $result[] = [
                    'value' => $this->normalizeChoiceValue($choice->value),
                    'label' => $this->translateChoiceLabel((string) $choice->label, $translationDomain),
                ];
            }
        }

        return $result;
    }

    private function translateChoiceLabel(string $label, string|false|null $translationDomain): string
    {
        if ($translationDomain === false) {
            return $label;
        }

        return $this->translator->trans($label, [], $translationDomain);
    }

    /**
     * Normalize a ChoiceView value.
     *
     * Symfony's ChoiceView always stores values as strings, but the API returns
     * entity IDs as integers. Convert numeric strings to integers so that
     * Ant Design's Select can match values with strict equality.
     */
    private function normalizeChoiceValue(string $value): string|int
    {
        if (ctype_digit($value)) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * Extract custom extra vars that form types set in buildView().
     *
     * Standard FormView vars (id, name, full_name, required, etc.) are excluded.
     * Only non-standard vars that form types add (e.g. relation_class) are included.
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
            'action', 'submitted', 'row_attr', 'errors', 'valid',
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
        foreach ($view->vars ?? [] as $key => $value) {
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
