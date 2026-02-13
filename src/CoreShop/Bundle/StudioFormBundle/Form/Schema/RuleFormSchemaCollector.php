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

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;

final class RuleFormSchemaCollector
{
    public function __construct(
        private readonly FormSchemaGenerator $generator,
    ) {
    }

    /**
     * Collect schemas for all given type names using a FormTypeRegistry.
     *
     * Schemas are keyed by their block prefix (e.g. 'coreshop_cart_price_rule_condition_amount'),
     * not by type name. This ensures the frontend cache lookup matches, since each rule engine
     * has different block prefixes for the same condition name (e.g. 'timespan').
     *
     * @param string[] $typeNames Type names to collect schemas for
     *
     * @return array<string, FormSchema> Map of block prefix to FormSchema
     */
    public function collectSchemas(FormTypeRegistryInterface $formTypeRegistry, array $typeNames): array
    {
        $schemas = [];

        foreach ($typeNames as $typeName) {
            if (!$formTypeRegistry->has($typeName, 'default')) {
                continue;
            }

            $formTypeClass = $formTypeRegistry->get($typeName, 'default');

            try {
                $schema = $this->generator->generate($formTypeClass);
                $schemas[$schema->blockPrefix] = $schema;
            } catch (\Throwable) {
                // Skip types that fail to generate (e.g. missing dependencies)
            }
        }

        return $schemas;
    }
}
