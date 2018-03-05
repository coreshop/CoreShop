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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableDiscountPriceCalculator implements PurchasableDiscountPriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $discountPriceCalculators;

    /**
     * @param PrioritizedServiceRegistryInterface $discountPriceCalculators
     */
    public function __construct(PrioritizedServiceRegistryInterface $discountPriceCalculators)
    {
        $this->discountPriceCalculators = $discountPriceCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $purchasable)
    {
        $price = 0;

        /**
         * @var $calculator PurchasableDiscountPriceCalculatorInterface
         */
        foreach ($this->discountPriceCalculators->all() as $calculator) {
            $actionPrice = $calculator->getDiscountPrice($purchasable);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;
            }
        }

        return $price;
    }
}
