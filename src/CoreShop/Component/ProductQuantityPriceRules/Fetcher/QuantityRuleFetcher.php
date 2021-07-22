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

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Fetcher\ValidRulesFetcherInterface;

class QuantityRuleFetcher implements QuantityRuleFetcherInterface
{
    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRulesFetcher;

    /**
     * @param ValidRulesFetcherInterface $validRulesFetcher
     */
    public function __construct(ValidRulesFetcherInterface $validRulesFetcher)
    {
        $this->validRulesFetcher = $validRulesFetcher;
    }

    public function fetch(QuantityRangePriceAwareInterface $subject, array $context)
    {
        $quantityPriceRules = $this->getQuantityPriceRulesForSubject($subject, $context);

        if (!is_array($quantityPriceRules)) {
            throw new NoRuleFoundException();
        }

        if (count($quantityPriceRules) === 0) {
            throw new NoRuleFoundException();
        }

        return $quantityPriceRules[0];
    }

    public function getQuantityPriceRulesForSubject(QuantityRangePriceAwareInterface $subject, array $context)
    {
        /** @var ProductQuantityPriceRuleInterface[] $rules */
        $rules = $this->validRulesFetcher->getValidRules($subject, $context);

        if (!is_array($rules)) {
            return [];
        }

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
