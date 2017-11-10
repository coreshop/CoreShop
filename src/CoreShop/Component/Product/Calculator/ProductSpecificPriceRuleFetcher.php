<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\PriceRuleInterface;
use Webmozart\Assert\Assert;

class ProductSpecificPriceRuleFetcher implements ProductPriceRuleFetcherInterface
{
    /**
     * @param ProductInterface $subject
     * @return PriceRuleInterface[]
     */
    public function getPriceRules(ProductInterface $subject) {
        /**
         * @var $subject ProductInterface
         */
        Assert::isInstanceOf($subject, ProductInterface::class);

        return $subject->getSpecificPriceRules();
    }
}