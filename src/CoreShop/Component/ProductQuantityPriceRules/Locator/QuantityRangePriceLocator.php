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

namespace CoreShop\Component\ProductQuantityPriceRules\Locator;

use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use Doctrine\Common\Collections\Collection;

class QuantityRangePriceLocator implements QuantityRangePriceLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate(Collection $ranges, int $quantity)
    {
        if ($ranges->isEmpty()) {
            return null;
        }

        $cheapestRangePrice = null;
        /** @var QuantityRangeInterface $range */
        foreach ($ranges as $range) {
            if ($range->getRangeFrom() > $quantity) {
                break;
            }
            $cheapestRangePrice = $range;
        }

        return $cheapestRangePrice;
    }
}
