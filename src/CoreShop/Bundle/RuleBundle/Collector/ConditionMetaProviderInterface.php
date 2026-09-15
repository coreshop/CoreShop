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
 * Contributes per condition type metadata for the Studio rule editor. Register implementations with the
 * tag "coreshop.rule.condition_meta_provider"; the ConditionMetaCollector merges the results of all providers.
 * The core ships no provider; bundles add what their editor extensions need, e.g. "indexable" and
 * "dimensions" for the badge on the condition card.
 */
interface ConditionMetaProviderInterface
{
    /**
     * @return array<string, mixed> metadata to merge for the type, empty when the provider has nothing to say
     */
    public function provide(ServiceRegistryInterface $conditionRegistry, string $type): array;
}
