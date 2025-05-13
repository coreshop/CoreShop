<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Product\Rule\Fetcher;

use CoreShop\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class MemoryCachedValidRuleFetcher implements ValidRulesFetcherInterface
{
    private array $checkedProducts = [];

    public function __construct(
        private ValidRulesFetcherInterface $validRuleFetcher,
        private RequestStack $requestStack,
    ) {
    }

    public function getValidRules(ProductInterface $product, array $context): array
    {
        if ($this->requestStack->getMainRequest() instanceof Request) {
            if (isset($this->checkedProducts[$product->getId()])) {
                return $this->checkedProducts[$product->getId()];
            }
        }

        $rules = $this->validRuleFetcher->getValidRules($product, $context);

        $this->checkedProducts[$product->getId()] = $rules;

        return $rules;
    }
}
