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

namespace CoreShop\Bundle\CoreBundle\Form\Schema;

use CoreShop\Bundle\CoreBundle\Form\Type\StoreSettingsType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\SectionSchema;

final class StoreSettingsSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_SECTIONS = [
        'guest_checkout' => 'base',
        'category_list_mode' => 'category',
        'category_list_per_page' => 'category',
        'category_list_per_page_default' => 'category',
        'category_list_include_subcategories' => 'category',
        'category_grid_per_page' => 'category',
        'category_grid_per_page_default' => 'category',
        'category_variant_mode' => 'category',
        'quote_prefix' => 'quote',
        'quote_suffix' => 'quote',
        'order_prefix' => 'order',
        'order_suffix' => 'order',
        'invoice_prefix' => 'invoice',
        'invoice_suffix' => 'invoice',
        'invoice_wkhtml' => 'invoice',
        'shipment_prefix' => 'shipping',
        'shipment_suffix' => 'shipping',
        'shipment_wkhtml' => 'shipping',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === StoreSettingsType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->sections = [
            new SectionSchema('base', 'coreshop_base', 100, true, false),
            new SectionSchema('category', 'coreshop_category', 90, true, true),
            new SectionSchema('quote', 'coreshop_quote', 80, true, true),
            new SectionSchema('order', 'coreshop_order', 70, true, true),
            new SectionSchema('invoice', 'coreshop_invoice', 60, true, true),
            new SectionSchema('shipping', 'coreshop_shipping', 50, true, true),
        ];

        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}
