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

final class RuleFormSchemaCollector
{
    public function __construct(
        private readonly FormSchemaGenerator $generator,
    ) {
    }

    /**
     * Collect schemas for a map of type name to form type class.
     *
     * @param array<string, string> $formTypeMap Map of type name => form type FQCN
     *
     * @return array<string, FormSchema> Map of type name to FormSchema
     */
    public function collectSchemas(array $formTypeMap): array
    {
        $schemas = [];

        foreach ($formTypeMap as $typeName => $formTypeClass) {
            try {
                $schemas[$typeName] = $this->generator->generate($formTypeClass);
            } catch (\Throwable) {
                // Skip types that fail to generate (e.g. missing dependencies)
            }
        }

        return $schemas;
    }
}
