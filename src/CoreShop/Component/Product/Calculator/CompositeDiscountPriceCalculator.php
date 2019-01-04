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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositeDiscountPriceCalculator implements ProductDiscountPriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $discountPriceCalculator;

    /**
     * @param PrioritizedServiceRegistryInterface $discountPriceCalculator
     */
    public function __construct($discountPriceCalculator)
    {
        $this->discountPriceCalculator = $discountPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(ProductInterface $subject, array $context)
    {
        $price = false;

        foreach ($this->discountPriceCalculator->all() as $calculator) {
            $actionPrice = $calculator->getDiscountPrice($subject, $context, $context);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;
            }
        }

        return $price;
    }
}
