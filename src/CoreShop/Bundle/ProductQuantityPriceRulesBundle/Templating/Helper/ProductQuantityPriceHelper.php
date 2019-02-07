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

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Calculator\ProductQuantityRangePriceCalculatorInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductQuantityPriceHelper extends Helper implements ProductQuantityPriceHelperInterface
{
    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @var ProductQuantityRangePriceCalculatorInterface
     */
    protected $productQuantityRangePriceCalculator;

    /**
     * @param ShopperContextInterface             $shopperContext
     * @param ProductQuantityRangePriceCalculatorInterface $productQuantityRangePriceCalculator
     */
    public function __construct(
        ShopperContextInterface $shopperContext,
        ProductQuantityRangePriceCalculatorInterface $productQuantityRangePriceCalculator
    ) {
        $this->shopperContext = $shopperContext;
        $this->productQuantityRangePriceCalculator = $productQuantityRangePriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product)
    {
        $productQuantityPriceRules = $this->productQuantityRangePriceCalculator->getQuantityPriceRulesForProduct($product, $this->shopperContext->getContext());

        if (count($productQuantityPriceRules) === 0) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product)
    {
        if ($this->hasActiveQuantityPriceRuleRanges($product) === false) {
            return [];
        }

        $productQuantityPriceRules = $this->productQuantityRangePriceCalculator->getQuantityPriceRulesForProduct($product, $this->shopperContext->getContext());
        $productQuantityPriceRule = $productQuantityPriceRules[0];

        return $productQuantityPriceRule->getRanges();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRuleRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $product)
    {
        $price = $this->productQuantityRangePriceCalculator->calculateRangePrice($range, $product, $this->shopperContext->getContext());

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_quantity_price';
    }
}
