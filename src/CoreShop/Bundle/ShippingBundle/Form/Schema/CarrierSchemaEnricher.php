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

namespace CoreShop\Bundle\ShippingBundle\Form\Schema;

use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\TabSchema;

final class CarrierSchemaEnricher implements FormSchemaEnricherInterface
{
    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === CarrierType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->tabs = [
            new TabSchema('settings', 'coreshop_settings', 10),
            new TabSchema('rules', 'coreshop_carrier_shipping_rules', 20),
        ];

        foreach ($schema->fields as $field) {
            if ($field->name === 'shippingRules') {
                $field->tab = 'rules';
            }
        }

        return $schema;
    }
}
