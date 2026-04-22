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

use CoreShop\Bundle\StudioFormBundle\Form\Type\Demo\FieldTypesDemoType;

final class FieldTypesDemoSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_SECTIONS = [
        'email' => 'text_inputs',
        'url' => 'text_inputs',
        'password' => 'text_inputs',
        'number' => 'numeric_date',
        'integer' => 'numeric_date',
        'date' => 'numeric_date',
        'datetime' => 'numeric_date',
        'time' => 'numeric_date',
        'color' => 'numeric_date',
        'range' => 'numeric_date',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === FieldTypesDemoType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->sections = [
            new SectionSchema('text_inputs', 'Text Inputs', 100, true, false),
            new SectionSchema('numeric_date', 'Numeric & Date', 90, true, true),
        ];

        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}
