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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Product\Rule\Fetcher;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

final class ValidProductSpecificPriceRuleFetcher implements ValidRulesFetcherInterface
{
    public function __construct(
        private RuleValidationProcessorInterface $ruleValidationProcessor,
    ) {
    }

    public function getValidRules(ProductInterface $product, array $context): array
    {
        $validRules = [];
        $rules = $product->getSpecificPriceRules();

        foreach ($rules as $rule) {
            if (!$this->ruleValidationProcessor->isValid($product, $rule, $context)) {
                continue;
            }

            $validRules[] = $rule;
        }

        return $validRules;
    }
}
