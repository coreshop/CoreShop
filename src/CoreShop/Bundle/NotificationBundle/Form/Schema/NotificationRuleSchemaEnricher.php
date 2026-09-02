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

namespace CoreShop\Bundle\NotificationBundle\Form\Schema;

use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class NotificationRuleSchemaEnricher implements FormSchemaEnricherInterface
{
    /**
     * Fields handled by dedicated UI components (RuleForm tabs, manual type selector).
     */
    private const array EXCLUDED_FIELDS = ['conditions', 'actions', 'type'];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === NotificationRuleType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->fields = array_values(
            array_filter(
                $schema->fields,
                static fn ($field) => !in_array($field->name, self::EXCLUDED_FIELDS, true),
            ),
        );

        return $schema;
    }
}
