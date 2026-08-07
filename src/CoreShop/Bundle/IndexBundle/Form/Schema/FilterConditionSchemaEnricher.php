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

namespace CoreShop\Bundle\IndexBundle\Form\Schema;

use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionBooleanType;
use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionCategoryMultiSelectType;
use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionCategorySelectType;
use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionMultiselectType;
use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionRangeType;
use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionSearchType;
use CoreShop\Bundle\IndexBundle\Form\Type\Filter\FilterConditionSelectType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class FilterConditionSchemaEnricher implements FormSchemaEnricherInterface
{
    /**
     * Map of form type class => field name => custom block prefix.
     *
     * @var array<class-string, array<string, string>>
     */
    private const FIELD_BLOCK_PREFIXES = [
        FilterConditionBooleanType::class => [
            'field' => 'coreshop_filter_index_field',
        ],
        FilterConditionSelectType::class => [
            'field' => 'coreshop_filter_index_field',
            'preSelect' => 'coreshop_filter_value_select',
        ],
        FilterConditionMultiselectType::class => [
            'field' => 'coreshop_filter_index_field',
            'preSelects' => 'coreshop_filter_value_multiselect',
        ],
        FilterConditionRangeType::class => [
            'field' => 'coreshop_filter_index_field',
            'preSelectMin' => 'coreshop_filter_value_select',
            'preSelectMax' => 'coreshop_filter_value_select',
        ],
        FilterConditionCategorySelectType::class => [
            'field' => 'coreshop_filter_index_field',
            'preSelect' => 'coreshop_filter_value_select',
        ],
        FilterConditionCategoryMultiSelectType::class => [
            'field' => 'coreshop_filter_index_field',
            'preSelects' => 'coreshop_filter_value_multiselect',
        ],
        FilterConditionSearchType::class => [
            'fields' => 'coreshop_filter_index_fields',
        ],
    ];

    public function supports(string $formTypeClass): bool
    {
        return isset(self::FIELD_BLOCK_PREFIXES[$formTypeClass]);
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $fieldMap = self::FIELD_BLOCK_PREFIXES[$formTypeClass] ?? [];

        foreach ($schema->fields as $field) {
            if (isset($fieldMap[$field->name])) {
                $field->blockPrefixes = array_merge(
                    $field->blockPrefixes,
                    [$fieldMap[$field->name]],
                );
            }
        }

        return $schema;
    }
}
