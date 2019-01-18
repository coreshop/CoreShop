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

namespace CoreShop\Component\TierPricing\Locator;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;

class TierPriceLocator implements TierPriceLocatorInterface
{
    /**
     * @param PurchasableInterface $purchasable
     * @param StoreInterface       $store
     * @param int                  $quantity
     *
     * @return ProductTierPriceRangeInterface|null
     */
    public function locate(PurchasableInterface $purchasable, StoreInterface $store, int $quantity)
    {
        $tierPrice = $purchasable->getTierPricing($store);

        if (!$tierPrice instanceof ProductTierPriceInterface) {
            return null;
        }

        if ($tierPrice->getRanges()->isEmpty()) {
            return null;
        }

        $cheapestTierPrice = null;
        /** @var ProductTierPriceRangeInterface $range */
        foreach ($tierPrice->getRanges() as $range) {
            if ($range->getRangeFrom() > $quantity) {
                break;
            }
            $cheapestTierPrice = $range;
        }

        return $cheapestTierPrice;
    }
}
