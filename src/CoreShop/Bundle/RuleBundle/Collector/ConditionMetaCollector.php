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
use CoreShop\Component\Rule\Condition\IndexableConditionCheckerInterface;

/**
 * Per condition type metadata for the Studio rule editor: whether the checker can be represented
 * in a precomputed index (see IndexableConditionCheckerInterface) and the dimensions it varies over.
 */
final class ConditionMetaCollector
{
    public const string NESTED_TYPE = 'nested';

    /**
     * @param string[] $types
     *
     * @return array<string, array{indexable: bool, dimensions: list<string>}>
     */
    public function collect(ServiceRegistryInterface $conditionRegistry, array $types): array
    {
        $meta = [];

        foreach ($types as $type) {
            $meta[$type] = $this->metaForType($conditionRegistry, $type);
        }

        return $meta;
    }

    /**
     * @return array{indexable: bool, dimensions: list<string>}
     */
    private function metaForType(ServiceRegistryInterface $conditionRegistry, string $type): array
    {
        if (self::NESTED_TYPE === $type) {
            // A nested condition is as indexable as its children, which the editor evaluates itself.
            return ['indexable' => true, 'dimensions' => []];
        }

        if (!$conditionRegistry->has($type)) {
            return ['indexable' => false, 'dimensions' => []];
        }

        $checker = $conditionRegistry->get($type);

        if (!$checker instanceof IndexableConditionCheckerInterface) {
            return ['indexable' => false, 'dimensions' => []];
        }

        // Metadata is per condition *type* (the editor shows it before a condition is configured), so the
        // shipped checkers report their dimensions independent of the configuration.
        return [
            'indexable' => true,
            'dimensions' => $checker->getPriceIndexDimensions([]),
        ];
    }
}
