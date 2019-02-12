<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Templating\Helper;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface ProductQuantityPriceHelperInterface
{
    /**
     * @param QuantityRangePriceAwareInterface $product
     *
     * @return bool
     */
    public function hasActiveQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product);

    /**
     * @param QuantityRangePriceAwareInterface $product
     *
     * @throws NoRuleFoundException
     * @return ProductQuantityPriceRuleInterface
     */
    public function getQuantityPriceRule(QuantityRangePriceAwareInterface $product);

    /**
     * @param QuantityRangePriceAwareInterface $product
     *
     * @throws NoRuleFoundException
     * @return array
     */
    public function getQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product);

    /**
     * @param QuantityRangeInterface           $range
     * @param QuantityRangePriceAwareInterface $product
     *
     * @throws NoPriceFoundException
     * @return int
     */
    public function getQuantityPriceRuleRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $product);
}
