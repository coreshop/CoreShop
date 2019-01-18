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
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceInterface;
use Symfony\Component\Templating\Helper\Helper;

class TierPricingHelper extends Helper implements TierPricingHelperInterface
{
    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(ShopperContextInterface $shopperContext
    ) {
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveTierPricing(ProductInterface $product)
    {
        if (!method_exists($product, 'getTierPricing')) {
            return false;
        }

        $tierPrice = $product->getTierPricing($this->shopperContext->getStore());
        if (!$tierPrice instanceof ProductTierPriceInterface) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceRanges(ProductInterface $product)
    {
        if ($this->hasActiveTierPricing($product) === false) {
            return [];
        }

        /** @var ProductTierPriceInterface $tierPrice */
        $tierPrice = $product->getTierPricing($this->shopperContext->getStore());
        $ranges = $tierPrice->getRanges();

        return $ranges->toArray();

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_tier_pricing';
    }
}
