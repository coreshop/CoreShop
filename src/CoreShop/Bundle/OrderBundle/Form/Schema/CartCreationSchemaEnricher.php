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

namespace CoreShop\Bundle\OrderBundle\Form\Schema;

use CoreShop\Bundle\OrderBundle\Form\Type\CartCreationType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\SectionSchema;

final class CartCreationSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_SECTIONS = [
        'store' => 'base',
        'currency' => 'base',
        'localeCode' => 'base',
        'items' => 'items',
    ];

    private const array HIDDEN_FIELDS = [
        'name',
        'customer',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === CartCreationType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->sections = array_merge($schema->sections, [
            new SectionSchema('base', 'coreshop_order_creation_base', 10),
            new SectionSchema('items', 'coreshop_order_creation_products', 20),
        ]);

        $schema->fields = array_values(array_filter($schema->fields, function ($field) {
            return !in_array($field->name, self::HIDDEN_FIELDS, true);
        }));

        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}
