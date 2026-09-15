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
namespace CoreShop\Bundle\RuleBundle\Collector;

use CoreShop\Component\Registry\ServiceRegistryInterface;

/**
 * Per condition type metadata for the Studio rule editor, merged from all registered
 * ConditionMetaProviderInterface services. Types without metadata are omitted.
 */
final class ConditionMetaCollector
{
    /**
     * @param iterable<ConditionMetaProviderInterface> $providers
     */
    public function __construct(
        private iterable $providers,
    ) {
    }

    /**
     * @param string[] $types
     *
     * @return array<string, array<string, mixed>>
     */
    public function collect(ServiceRegistryInterface $conditionRegistry, array $types): array
    {
        $meta = [];

        foreach ($types as $type) {
            $typeMeta = [];

            foreach ($this->providers as $provider) {
                $typeMeta = array_merge($typeMeta, $provider->provide($conditionRegistry, $type));
            }

            if ([] !== $typeMeta) {
                $meta[$type] = $typeMeta;
            }
        }

        return $meta;
    }
}
