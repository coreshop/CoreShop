<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\ProductQuantityPriceRules\Rule\Fetcher;

use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

final class ValidProductQuantityPriceRuleFetcher implements ValidRulesFetcherInterface
{
    public function __construct(private RuleValidationProcessorInterface $ruleValidationProcessor)
    {
    }

    public function getValidRules(QuantityRangePriceAwareInterface $product, array $context): array
    {
        $validRules = [];
        $rules = $product->getQuantityPriceRules();

        foreach ($rules as $rule) {
            if (!$this->ruleValidationProcessor->isValid($product, $rule, $context)) {
                continue;
            }

            $validRules[] = $rule;
        }

        return $validRules;
    }
}
