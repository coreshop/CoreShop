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

class CompositePurchasablePriceCalculator implements PurchasablePriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $calculators;

    /**
     * @param PrioritizedServiceRegistryInterface $calculators
     */
    public function __construct(PrioritizedServiceRegistryInterface $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $purchasable, $includingDiscounts = false)
    {
        $price = false;

        /**
         * @var $calculator PurchasablePriceCalculatorInterface
         */
        foreach ($this->calculators->all() as $calculator) {
            $actionPrice = $calculator->getPrice($purchasable, $includingDiscounts);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;
            }
        }

        return $price;
    }
}
