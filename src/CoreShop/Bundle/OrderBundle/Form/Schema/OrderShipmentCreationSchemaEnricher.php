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

use CoreShop\Bundle\OrderBundle\Form\Type\Studio\OrderShipmentCreationType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class OrderShipmentCreationSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array HIDDEN_FIELDS = [
        'id',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === OrderShipmentCreationType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->fields = array_values(array_filter($schema->fields, function ($field) {
            return !in_array($field->name, self::HIDDEN_FIELDS, true);
        }));

        return $schema;
    }
}
