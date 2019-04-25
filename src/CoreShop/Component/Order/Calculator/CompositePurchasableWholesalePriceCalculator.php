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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableWholesalePriceCalculator implements PurchasableWholesalePriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $wholesalePriceCalculators;

    /**
     * @param PrioritizedServiceRegistryInterface $wholesalePriceCalculators
     */
    public function __construct(PrioritizedServiceRegistryInterface $wholesalePriceCalculators)
    {
        $this->wholesalePriceCalculators = $wholesalePriceCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchasableWholesalePrice(PurchasableInterface $purchasable, array $context)
    {
        $price = null;

        /**
         * @var PurchasableWholesalePriceCalculatorInterface $calculator
         */
        foreach ($this->wholesalePriceCalculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getPurchasableWholesalePrice($purchasable, $context);
                $price = $actionPrice;
            } catch (NoPurchasableWholesalePriceFoundException $ex) {
            }
        }

        if (null === $price) {
            throw new NoPurchasableWholesalePriceFoundException(__CLASS__);
        }

        return $price;
    }
}
