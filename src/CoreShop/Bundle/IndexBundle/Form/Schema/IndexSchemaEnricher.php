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

use CoreShop\Bundle\IndexBundle\Form\Type\IndexType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class IndexSchemaEnricher implements FormSchemaEnricherInterface
{
    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === IndexType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        // Remove columns — rendered in a dedicated tab.
        // Remove configuration — dynamically added by worker type, rendered separately.
        $schema->fields = array_values(
            array_filter($schema->fields, static fn ($field) => !in_array($field->name, ['columns', 'configuration'], true)),
        );

        return $schema;
    }
}
