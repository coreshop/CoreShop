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

namespace CoreShop\Bundle\PaymentBundle\Form\Schema;

use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\TabSchema;

final class PaymentProviderSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_ORDER = [
        'identifier',
        'position',
        'active',
        'logo',
        'gatewayConfig',
        'translations',
        'stores',
        'paymentProviderRules',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === PaymentProviderType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->tabs = [
            new TabSchema('settings', 'coreshop_settings', 10),
            new TabSchema('rules', 'coreshop_payment_provider_rules', 20),
        ];

        foreach ($schema->fields as $field) {
            if ($field->name === 'paymentProviderRules') {
                $field->tab = 'rules';
            }
        }

        // Reorder fields to match the desired layout.
        $orderMap = array_flip(self::FIELD_ORDER);
        usort($schema->fields, static function ($a, $b) use ($orderMap): int {
            $posA = $orderMap[$a->name] ?? 999;
            $posB = $orderMap[$b->name] ?? 999;

            return $posA <=> $posB;
        });

        return $schema;
    }
}
