<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Exception\NoPurchasableRetailPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableRetailPriceCalculator implements PurchasableRetailPriceCalculatorInterface
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
    public function getRetailPrice(PurchasableInterface $purchasable, array $context)
    {
        $price = null;

        /**
         * @var PurchasableRetailPriceCalculatorInterface $calculator
         */
        foreach ($this->calculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getRetailPrice($purchasable, $context);
                $price = $actionPrice;
            } catch (NoPurchasableRetailPriceFoundException $ex) {
            }
        }

        if (null === $price) {
            throw new NoPurchasableRetailPriceFoundException(__CLASS__);
        }

        return $price;
    }
}
