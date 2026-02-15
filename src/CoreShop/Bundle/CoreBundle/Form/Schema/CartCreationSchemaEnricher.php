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

use CoreShop\Bundle\OrderBundle\Form\Type\CartCreationType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\SectionSchema;

final class CartCreationSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_SECTIONS = [
        'shippingAddress' => 'address',
        'invoiceAddress' => 'address',
        'carrier' => 'shipping',
        'paymentProvider' => 'payment',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === CartCreationType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->sections = array_merge($schema->sections, [
            new SectionSchema('address', 'coreshop_order_creation_address', 30),
            new SectionSchema('shipping', 'coreshop_order_creation_shipping', 40),
            new SectionSchema('payment', 'coreshop_order_creation_payment', 50),
        ]);

        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}
