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
use Pimcore\Cache;
use Webmozart\Assert\Assert;

class CachedProductPriceRuleFetcher implements ProductPriceRuleFetcherInterface
{
    /**
     * @var ProductPriceRuleFetcherInterface
     */
    protected $productPriceRuleFetcher;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param ProductPriceRuleFetcherInterface $productPriceRuleFetcher
     */
    public function __construct(ProductPriceRuleFetcherInterface $productPriceRuleFetcher)
    {
        $this->productPriceRuleFetcher = $productPriceRuleFetcher;
    }

    /**
     * @param ProductInterface $subject
     * @return PriceRuleInterface[]
     */
    public function getPriceRules(ProductInterface $subject)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        $cacheKey = md5(get_class($subject) . "_" . get_class($this->productPriceRuleFetcher) . "_" . $subject->getId());

        if (!array_key_exists($cacheKey, $this->cache)) {
            $rules = $this->productPriceRuleFetcher->getPriceRules($subject);

            $this->cache[$cacheKey] = $rules;

            return $rules;
        }

        return $this->cache[$cacheKey];
    }
}