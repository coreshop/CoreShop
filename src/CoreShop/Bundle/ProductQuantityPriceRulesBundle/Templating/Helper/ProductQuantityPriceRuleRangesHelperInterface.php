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

declare(strict_types=1);

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Templating\Helper;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use Doctrine\Common\Collections\Collection;

interface ProductQuantityPriceRuleRangesHelperInterface
{
    /**
     * @param QuantityRangePriceAwareInterface $product
     * @param array                            $context
     *
     * @return bool
     */
    public function hasActiveQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product, array $context): bool;

    /**
     * @param QuantityRangePriceAwareInterface $product
     * @param array                            $context
     *
     * @throws NoRuleFoundException
     *
     * @return ProductQuantityPriceRuleInterface
     */
    public function getQuantityPriceRule(QuantityRangePriceAwareInterface $product, array $context): ProductQuantityPriceRuleInterface;

    /**
     * @param QuantityRangePriceAwareInterface $product
     * @param array                            $context
     *
     * @throws NoRuleFoundException
     *
     * @return Collection
     */
    public function getQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product, array $context): Collection;
}
