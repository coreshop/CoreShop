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

namespace CoreShop\Bundle\TierPricingBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use CoreShop\Component\TierPricing\Rule\Calculator\ProductTierPriceCalculatorInterface;
use Symfony\Component\Templating\Helper\Helper;

class TierPricingHelper extends Helper implements TierPricingHelperInterface
{
    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @var ProductTierPriceCalculatorInterface
     */
    protected $productTierPriceCalculator;

    /**
     * @param ShopperContextInterface             $shopperContext
     * @param ProductTierPriceCalculatorInterface $productTierPriceCalculator
     */
    public function __construct(
        ShopperContextInterface $shopperContext,
        ProductTierPriceCalculatorInterface $productTierPriceCalculator
    ) {
        $this->shopperContext = $shopperContext;
        $this->productTierPriceCalculator = $productTierPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveTierPricing(TierPriceAwareInterface $product)
    {
        $tierPriceRules = $this->productTierPriceCalculator->getTierPriceRulesForProduct($product, $this->shopperContext->getContext());

        if (count($tierPriceRules) === 0) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceRanges(TierPriceAwareInterface $product)
    {
        if ($this->hasActiveTierPricing($product) === false) {
            return [];
        }

        $tierPriceRules = $this->productTierPriceCalculator->getTierPriceRulesForProduct($product, $this->shopperContext->getContext());
        $tierPriceRule = $tierPriceRules[0];

        return $tierPriceRule->getRanges();
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculatedRangePrice(ProductTierPriceRangeInterface $range, TierPriceAwareInterface $product)
    {
        $price = $this->productTierPriceCalculator->calculateRangePrice($range, $product, $this->shopperContext->getContext());

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_tier_pricing';
    }
}
