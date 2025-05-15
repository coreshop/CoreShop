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

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Fetcher\ValidRulesFetcherInterface;

class QuantityRuleFetcher implements QuantityRuleFetcherInterface
{
    public function __construct(
        private ValidRulesFetcherInterface $validRulesFetcher,
    ) {
    }

    public function fetch(QuantityRangePriceAwareInterface $subject, array $context): ProductQuantityPriceRuleInterface
    {
        $quantityPriceRules = $this->getQuantityPriceRulesForSubject($subject, $context);

        if (count($quantityPriceRules) === 0) {
            throw new NoRuleFoundException();
        }

        return $quantityPriceRules[0];
    }

    public function getQuantityPriceRulesForSubject(QuantityRangePriceAwareInterface $subject, array $context): array
    {
        /** @var ProductQuantityPriceRuleInterface[] $rules */
        $rules = $this->validRulesFetcher->getValidRules($subject, $context);

        // sort by priority: higher priority first!
        usort($rules, function (ProductQuantityPriceRuleInterface $a, ProductQuantityPriceRuleInterface $b) {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;
            }

            return ($a->getPriority() > $b->getPriority()) ? -1 : 1;
        });

        return $rules;
    }
}
