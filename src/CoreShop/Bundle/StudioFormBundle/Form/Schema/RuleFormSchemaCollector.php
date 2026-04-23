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

use Psr\Log\LoggerInterface;

final class RuleFormSchemaCollector
{
    public function __construct(
        private readonly FormSchemaGenerator $generator,
        private readonly ?LoggerInterface $logger = null,
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
    public function collectSchemas(object $formTypeRegistry, array $typeNames): array
    {
        return $this->collectSchemasWithTypeMap($formTypeRegistry, $typeNames)['schemas'];
    }

    /**
     * Collect schemas and the corresponding block prefix mapping by rule type.
     *
     * @param string[] $typeNames
     *
     * @return array{
     *   schemas: array<string, FormSchema>,
     *   schemaByType: array<string, string>
     * }
     */
    public function collectSchemasWithTypeMap(object $formTypeRegistry, array $typeNames): array
    {
        $hasTypeCallable = \Closure::fromCallable([$formTypeRegistry, 'has']);
        $getTypeCallable = \Closure::fromCallable([$formTypeRegistry, 'get']);

        $schemas = [];
        $schemaByType = [];

        foreach ($typeNames as $typeName) {
            if (!(bool) $hasTypeCallable($typeName, 'default')) {
                continue;
            }

            $formTypeClass = $getTypeCallable($typeName, 'default');
            if (!is_string($formTypeClass)) {
                continue;
            }

            try {
                $schema = $this->generator->generate($formTypeClass);
                $schemas[$schema->blockPrefix] = $schema;
                $schemaByType[$typeName] = $schema->blockPrefix;
            } catch (\Throwable $e) {
                $this->logger?->warning(
                    sprintf(
                        'Failed to generate Studio form schema for rule type "%s" (%s): %s',
                        $typeName,
                        $formTypeClass,
                        $e->getMessage(),
                    ),
                );
            }
        }

        return [
            'schemas' => $schemas,
            'schemaByType' => $schemaByType,
        ];
    }
}
