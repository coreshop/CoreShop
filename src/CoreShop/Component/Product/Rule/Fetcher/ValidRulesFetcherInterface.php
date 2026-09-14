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

namespace CoreShop\Component\Product\Rule\Fetcher;

use CoreShop\Component\Product\Model\ProductInterface;

interface ValidRulesFetcherInterface
{
    /**
     * Context key; when set (truthy) implementations must not serve rules from a per product cache,
     * because the caller evaluates the same product under several contexts (e.g. the price index).
     */
    public const string CONTEXT_NO_CACHE = 'no_rule_cache';

    public function getValidRules(ProductInterface $product, array $context): array;
}
