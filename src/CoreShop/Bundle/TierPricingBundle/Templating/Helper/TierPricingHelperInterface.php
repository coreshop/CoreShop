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

use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;

interface TierPricingHelperInterface
{
    /**
     * @param TierPriceAwareInterface $product
     *
     * @return bool
     */
    public function hasActiveTierPricing(TierPriceAwareInterface $product);

    /**
     * @param TierPriceAwareInterface $product
     *
     * @return array
     */
    public function getTierPriceRanges(TierPriceAwareInterface $product);

    /**
     * @param ProductTierPriceRangeInterface $range
     * @param TierPriceAwareInterface        $product
     *
     * @return mixed
     */
    public function getCalculatedRangePrice(ProductTierPriceRangeInterface $range, TierPriceAwareInterface $product);

}
