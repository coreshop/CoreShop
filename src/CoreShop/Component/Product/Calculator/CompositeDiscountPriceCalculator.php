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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Exception\NoDiscountPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositeDiscountPriceCalculator implements ProductDiscountPriceCalculatorInterface
{
    protected PrioritizedServiceRegistryInterface $discountPriceCalculator;

    public function __construct(PrioritizedServiceRegistryInterface $discountPriceCalculator)
    {
        $this->discountPriceCalculator = $discountPriceCalculator;
    }

    public function getDiscountPrice(ProductInterface $subject, array $context): int
    {
        $price = null;

        /**
         * @var ProductDiscountPriceCalculatorInterface $calculator
         */
        foreach ($this->discountPriceCalculator->all() as $calculator) {
            try {
                $actionPrice = $calculator->getDiscountPrice($subject, $context);
                $price = $actionPrice;
            } catch (NoDiscountPriceFoundException $ex) {
            }
        }

        if (null === $price) {
            throw new NoDiscountPriceFoundException(__CLASS__);
        }

        return $price;
    }
}
