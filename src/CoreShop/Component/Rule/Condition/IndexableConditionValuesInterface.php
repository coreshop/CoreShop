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

namespace CoreShop\Component\Rule\Condition;

/**
 * An indexable condition whose dimension is not expanded over all existing values but only over the
 * ids the condition explicitly references (e.g. the customers or companies selected in the rule).
 */
interface IndexableConditionValuesInterface extends IndexableConditionCheckerInterface
{
    /**
     * @return array<string, list<int>> dimension name => referenced ids
     */
    public function getPriceIndexDimensionValues(array $configuration): array;
}
