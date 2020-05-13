<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Exception\NoPurchasableDiscountPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableDiscountPriceCalculator implements PurchasableDiscountPriceCalculatorInterface
{
    protected $discountPriceCalculators;

    public function __construct(PrioritizedServiceRegistryInterface $discountPriceCalculators)
    {
        $this->discountPriceCalculators = $discountPriceCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $purchasable, array $context): int
    {
        $price = null;

        /**
         * @var PurchasableDiscountPriceCalculatorInterface $calculator
         */
        foreach ($this->discountPriceCalculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getDiscountPrice($purchasable, $context);
                $price = $actionPrice;
            } catch (NoPurchasableDiscountPriceFoundException $ex) {
            }
        }

        if (null === $price) {
            throw new NoPurchasableDiscountPriceFoundException(__CLASS__);
        }

        return $price;
    }
}
