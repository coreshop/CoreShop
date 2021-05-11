<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Fetcher\ValidRulesFetcherInterface;

interface QuantityRuleFetcherInterface
{
    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param array                            $context
     *
     * @throws NoRuleFoundException
     *
     * @return ProductQuantityPriceRuleInterface
     */
    public function fetch(QuantityRangePriceAwareInterface $subject, array $context);

    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param array                            $context
     *
     * @return array|ProductQuantityPriceRuleInterface[]
     */
    public function getQuantityPriceRulesForSubject(QuantityRangePriceAwareInterface $subject, array $context);
}
