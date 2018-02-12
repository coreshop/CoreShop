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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

final class ProductPriceCalculator implements ProductPriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $priceCalculatorRegistry;

    /**
     * ProductPriceCalculator constructor.
     *
     * @param PrioritizedServiceRegistryInterface $priceCalculatorRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $priceCalculatorRegistry)
    {
        $this->priceCalculatorRegistry = $priceCalculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ProductInterface $subject)
    {
        $price = false;

        foreach ($this->priceCalculatorRegistry->all() as $calculator) {
            $calcPrice = $calculator->getPrice($subject);

            if (false !== $calcPrice && null !== $calcPrice) {
                $price = $calcPrice;
            }
        }

        return $price;
    }

    public function getDiscountPrice(ProductInterface $subject)
    {
         $price = false;

        foreach ($this->priceCalculatorRegistry->all() as $calculator) {
            $calcPrice = $calculator->getDiscountPrice($subject);

            if (false !== $calcPrice && null !== $calcPrice) {
                $price = $calcPrice;
            }
        }

        return $price;
    }


    /**
     * {@inheritdoc}
     */
    public function getDiscount(ProductInterface $subject, $price)
    {
        $discount = 0;

        foreach ($this->priceCalculatorRegistry->all() as $calculator) {
            $discount += $calculator->getDiscount($subject, $price);
        }

        return $discount;
    }
}
